require("dotenv").config();

const express = require("express");
const path = require("path");

const vpsRoutes = require("./routes/vps");

const app = express();

app.set("view engine", "ejs");
app.set("views", path.join(__dirname, "views"));

app.use(express.urlencoded({ extended: true }));
app.use(express.json());
app.use(express.static(path.join(__dirname, "public")));

app.use("/", vpsRoutes);

// 404 Code
app.use((req, res) => {
  res.status(404).send("404 - Halaman tidak ditemukan");
});

// Global error handler
app.use((err, req, res, next) => {
  console.error("[unhandled error]", err);
  res.status(500).send("500 - Terjadi kesalahan pada server");
});

const PORT = process.env.PORT || 3000;

app.listen(PORT, () => {
  console.log(`Server ready on http://IP-SERVER:${PORT}`);
});
