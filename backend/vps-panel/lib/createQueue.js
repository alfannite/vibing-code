// Proxmox's /cluster/nextid + clone are two separate API calls.
// If two "create VPS" requests happen at the same time, both could get
// the same "next free" VMID before either one actually uses it.
// This queue forces creation jobs to run one at a time so that never happens.
//
// Note: this only protects against races *within this Node process*.
// If you ever run multiple instances of this app behind a load balancer,
// swap this for a real distributed lock (e.g. Redis) around the same block.

let tail = Promise.resolve();

function enqueue(task) {
  const result = tail.then(() => task());
  // Never let a rejected task stall the queue for the next job.
  tail = result.catch(() => {});
  return result;
}

module.exports = { enqueue };
