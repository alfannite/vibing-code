const express = require("express");
const router = express.Router();

const {
  getVersion,
  getNextVmid,
  cloneVm,
  configureVm,
  resizeDisk,
  startVm,
  waitForTask
} = require("../config/proxmox");
const { enqueue } = require("../lib/createQueue");

// Paket yang tersedia. Ubah/tambah di sini saja, form & validasi ikut otomatis.
// `disk` dalam GB (dipakai untuk resize, harus >= ukuran disk di template).
const PACKAGES = {
  elite: {
    label: "Elite (2 Core, 4GB RAM, 20GB Disk)",
    cores: 2,
    memory: 4096,
    disk: 20
  },
  epic: {
    label: "Epic (4 Core, 8GB RAM, 40GB Disk)",
    cores: 4,
    memory: 8192,
    disk: 40
  }
};

function isValidHostname(hostname) {
  // huruf kecil, angka, tanda hubung; tidak diawali/diakhiri "-"
  return /^[a-z0-9]([a-z0-9-]{0,61}[a-z0-9])?$/.test(hostname);
}

// GET / -> tampilkan form
router.get("/", (req, res) => {
  res.render("form", { packages: PACKAGES, error: null });
});

// GET /test-proxmox -> cek koneksi ke Proxmox
router.get("/test-proxmox", async (req, res) => {
  try {
    const data = await getVersion();
    res.json({ success: true, data });
  } catch (error) {
    console.error("[test-proxmox]", error.response?.data || error.message);
    res.status(error.response?.status || 500).json({
      success: false,
      message: error.response?.data || error.message
    });
  }
});

// POST /create -> clone VM dari golden template + terapkan cloud-init
router.post("/create", async (req, res) => {
  const { hostname, username, password, sshkey, package: pkgKey } = req.body;

  // ---------- Validasi input ----------
  if (!hostname || !username || !password || !pkgKey) {
    return res.status(400).render("form", {
      packages: PACKAGES,
      error: "Semua field wajib diisi (SSH key boleh dikosongkan)."
    });
  }

  if (!isValidHostname(hostname)) {
    return res.status(400).render("form", {
      packages: PACKAGES,
      error: "Hostname tidak valid. Gunakan huruf kecil, angka, dan tanda hubung (-) saja."
    });
  }

  if (password.length < 8) {
    return res.status(400).render("form", {
      packages: PACKAGES,
      error: "Password minimal 8 karakter."
    });
  }

  const pkg = PACKAGES[pkgKey];
  if (!pkg) {
    return res.status(400).render("form", {
      packages: PACKAGES,
      error: "Paket yang dipilih tidak valid."
    });
  }

  // ---------- Proses ke Proxmox (diserialisasi lewat queue) ----------
  try {
    const result = await enqueue(async () => {
      // 1. Ambil VMID unik berikutnya
      const vmid = await getNextVmid();

      // 2. Clone dari golden template (full clone)
      const cloneUpid = await cloneVm({ newid: vmid, name: hostname });
      await waitForTask(cloneUpid, { timeoutMs: 180000 });

      // 3. Set spek (cores/memory) + cloud-init (user/password/sshkey)
      await configureVm({
        vmid,
        cores: pkg.cores,
        memory: pkg.memory,
        ciuser: username,
        cipassword: password,
        sshkeys: sshkey
      });

      // 4. Resize disk sesuai paket (harus >= ukuran disk asli template)
      await resizeDisk({ vmid, disk: "scsi0", size: `${pkg.disk}G` });

      // 5. Start VM, cloud-init akan jalan otomatis saat boot pertama
      const startUpid = await startVm({ vmid });
      const startTask = await waitForTask(startUpid, { timeoutMs: 60000 });

      return {
        vmid,
        success: startTask.exitstatus === "OK",
        taskStatus: startTask.exitstatus
      };
    });

    return res.render("result", {
      success: result.success,
      vmid: result.vmid,
      hostname,
      username,
      package: pkg.label,
      taskStatus: result.taskStatus,
      error: result.success ? null : `VM dibuat tapi gagal start: ${result.taskStatus}`
    });
  } catch (error) {
    console.error("[create]", error.response?.data || error.message);

    const message =
      error.response?.data?.errors
        ? JSON.stringify(error.response.data.errors)
        : error.response?.data?.message || error.message;

    return res.status(500).render("result", {
      success: false,
      vmid: null,
      hostname,
      username,
      package: pkg.label,
      taskStatus: null,
      error: message
    });
  }
});

module.exports = router;
