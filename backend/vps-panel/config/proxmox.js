const axios = require("axios");
const https = require("https");

const {
  PVE_HOST,
  PVE_NODE,
  PVE_TOKEN_ID,
  PVE_TOKEN_SECRET,
  PVE_STORAGE = "local-lvm",
  PVE_TEMPLATE_VMID, // VMID of the golden template (must already exist & be a Proxmox "template")
  PVE_BRIDGE = "vmbr0"
} = process.env;

if (!PVE_HOST || !PVE_NODE || !PVE_TOKEN_ID || !PVE_TOKEN_SECRET || !PVE_TEMPLATE_VMID) {
  console.warn(
    "[proxmox] WARNING: satu atau lebih env var (PVE_HOST, PVE_NODE, PVE_TOKEN_ID, PVE_TOKEN_SECRET, PVE_TEMPLATE_VMID) belum diset."
  );
}

// Axios instance dengan auth token Proxmox + self-signed cert bypass
const proxmox = axios.create({
  baseURL: `${PVE_HOST}/api2/json`,
  httpsAgent: new https.Agent({ rejectUnauthorized: false }),
  headers: {
    Authorization: `PVEAPIToken=${PVE_TOKEN_ID}=${PVE_TOKEN_SECRET}`
  },
  timeout: 10000
});

/** Cek koneksi & versi Proxmox VE */
async function getVersion() {
  const { data } = await proxmox.get("/version");
  return data.data;
}

/**
 * Ambil VMID berikutnya yang tersedia di cluster.
 * Proxmox menjamin ini unik di seluruh cluster (mirip auto-increment,
 * tapi mengisi ulang ID yang sudah dihapus juga).
 * NOTE: panggilan ini + clone() bukan satu transaksi atomik, makanya
 * di routes/vps.js proses create dibungkus queue (lib/createQueue.js).
 */
async function getNextVmid() {
  const { data } = await proxmox.get("/cluster/nextid");
  return data.data;
}

/**
 * Clone VM dari golden template (full clone, bukan linked clone,
 * supaya VM baru independen dari template & storage template).
 * Return UPID (task id) untuk dipantau progress-nya.
 */
async function cloneVm({ newid, name }) {
  const { data } = await proxmox.post(`/nodes/${PVE_NODE}/qemu/${PVE_TEMPLATE_VMID}/clone`, {
    newid,
    name,
    full: 1,
    storage: PVE_STORAGE
  });
  return data.data; // UPID
}

/**
 * Set spek VM (cores/memory) + parameter cloud-init (user, password, ssh key, network).
 * Ini langsung ditulis ke drive cloud-init VM tsb; akan diterapkan saat VM boot.
 */
async function configureVm({ vmid, cores, memory, ciuser, cipassword, sshkeys, ipconfig0 = "ip=dhcp" }) {
  const params = {
    cores,
    memory,
    ciuser,
    cipassword,
    ipconfig0
  };

  if (sshkeys && sshkeys.trim() !== "") {
    // Proxmox expects the key(s) URL-encoded in this field
    params.sshkeys = encodeURIComponent(sshkeys.trim());
  }

  const { data } = await proxmox.put(`/nodes/${PVE_NODE}/qemu/${vmid}/config`, params);
  return data.data;
}

/**
 * Resize disk VM. `size` contoh: "20G" (absolut) atau "+5G" (nambah dari ukuran sekarang).
 * `disk` adalah nama disk di VM config, biasanya "scsi0" untuk template cloud-init standar.
 */
async function resizeDisk({ vmid, disk = "scsi0", size }) {
  const { data } = await proxmox.put(`/nodes/${PVE_NODE}/qemu/${vmid}/resize`, {
    disk,
    size
  });
  return data.data;
}

/** Nyalakan VM. Return UPID. */
async function startVm({ vmid }) {
  const { data } = await proxmox.post(`/nodes/${PVE_NODE}/qemu/${vmid}/status/start`);
  return data.data;
}

/** Ambil status task Proxmox berdasarkan UPID */
async function getTaskStatus(upid) {
  const { data } = await proxmox.get(
    `/nodes/${PVE_NODE}/tasks/${encodeURIComponent(upid)}/status`
  );
  return data.data;
}

/**
 * Poll status task sampai selesai ("stopped") atau timeout.
 * Return object status terakhir, termasuk field `exitstatus` ("OK" jika sukses).
 */
async function waitForTask(upid, { intervalMs = 1500, timeoutMs = 60000 } = {}) {
  const start = Date.now();

  while (Date.now() - start < timeoutMs) {
    const status = await getTaskStatus(upid);

    if (status.status === "stopped") {
      return status;
    }

    await new Promise((resolve) => setTimeout(resolve, intervalMs));
  }

  throw new Error(`Timeout menunggu task Proxmox selesai (>${timeoutMs}ms)`);
}

module.exports = {
  proxmox,
  PVE_NODE,
  PVE_TEMPLATE_VMID,
  getVersion,
  getNextVmid,
  cloneVm,
  configureVm,
  resizeDisk,
  startVm,
  getTaskStatus,
  waitForTask
};
