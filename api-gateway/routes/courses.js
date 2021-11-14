const express = require("express");
const router = express.Router();
const verifyToken = require("../middleware/verifyToken");
const mentorHandler = require("./handler/courses/mentor");
const courseHandler = require("./handler/courses/course");

/* Mentor route listing. */
router.get("/mentor/", verifyToken, mentorHandler.read);
router.post("/mentor/", verifyToken, mentorHandler.create);
router.get("/mentor/:id", verifyToken, mentorHandler.show);
router.put("/mentor/:id", verifyToken, mentorHandler.update);
router.delete("/mentor/:id", verifyToken, mentorHandler.destroy);

/* Course route listing. */
router.get("/catalog", courseHandler.get);
router.post("/catalog", verifyToken, courseHandler.create);
router.get("/catalog/:id", courseHandler.show);
router.put("/catalog/:id", verifyToken, courseHandler.update);
router.delete("/catalog/:id", verifyToken, courseHandler.destroy);

module.exports = router;