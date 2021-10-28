const express = require("express");
const router = express.Router();
const isBase64 = require("is-base64");
const base64Img = require("base64-img");
const { Media } = require("../models");
const fs = require("fs");

router.get("/", async(req, res) => {
    const media = await Media.findAll({
        attributes: ["id", "image"],
    });

    const mediaURL = media.map((m) => {
        m.image = `${req.get("host")}/${m.image}`;
        return m;
    });

    return res.json({
        status: "success",
        data: mediaURL,
    });
});

router.post("/", (req, res) => {
    const image = req.body.image;

    // mimeType = "data:image/png;base64" base64 format harus ada pada string base64nya
    if (!isBase64(image, { mimeRequired: true })) {
        return res.status(400).json({ status: "error", message: "invalid base64" });
    }

    base64Img.img(image, "./public/images", Date.now(), async(err, filepath) => {
        if (err) {
            return res.status(400).json({ status: "error", message: err.message });
        }

        const filename = filepath.split("\\").pop().split("/").pop();

        const media = await Media.create({ image: `images/${filename}` });

        return res.json({
            status: "success",
            data: {
                id: media.id,
                image: `${req.get("host")}/images/${filename}`,
            },
        });
    });
});

router.delete("/:id", async(req, res) => {
    // Get id by param (.params)
    const id = req.params.id;
    // Cek di database gambarnya berdasrakan id tersebut
    const media = await Media.findByPk(id);
    // Cek dulu apakah di db ada apa nggak, kalau gk ada berikan respon error
    if (!media) {
        return res.status(404).json({ status: "error", message: "media not found" });
    }
    // Jika ada maka akan hapus gambar dari folder public (gunakan fs package bawaan node js untuk akses file)
    //  Beri exception kalau gk ada id X maka respon error
    fs.unlink(`./public/${media.image}`, async(err) => {
        if (err) {
            return res.status(400).json({ status: "error", message: err.message });
        }
        // Kalau idnya ditemukan setelah di hapus dari folder yg di database juga dihapus
        await media.destroy();
        // Kemudian kalau berhasil ngapus kasih respon
        return res.json({
            status: "success",
            message: "image deleted",
        });
    });
});

module.exports = router;