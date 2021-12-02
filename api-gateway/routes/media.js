const express = require("express");
const router = express.Router();
const mediaHandler = require("./handler/media");
const verifyToken = require("../middleware/verifyToken");
const permission = require("../middleware/permission");

router.post("/", verifyToken, permission("admin", "student"), mediaHandler.create);
router.get("/", verifyToken, permission("admin", "student"), mediaHandler.read);
router.delete("/:id", verifyToken, permission("admin", "student"), mediaHandler.destroy);

module.exports = router;