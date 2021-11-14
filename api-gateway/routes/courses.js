const express = require("express");
const router = express.Router();
const verifyToken = require("../middleware/verifyToken");
const mentorHandler = require("./handler/courses/mentor");
const courseHandler = require("./handler/courses/course");
const chapterHandler = require("./handler/courses/chapter");
const lessonHandler = require("./handler/courses/lesson");

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

/* Chapter route listing. */
router.get("/chapter", verifyToken, chapterHandler.get);
router.post("/chapter", verifyToken, chapterHandler.create);
router.get("/chapter/:id", verifyToken, chapterHandler.show);
router.put("/chapter/:id", verifyToken, chapterHandler.update);
router.delete("/chapter/:id", verifyToken, chapterHandler.destroy);

/* Lesson route listing. */
router.get("/lesson", verifyToken, lessonHandler.get);
router.post("/lesson", verifyToken, lessonHandler.create);
router.get("/lesson/:id", verifyToken, lessonHandler.show);
router.put("/lesson/:id", verifyToken, lessonHandler.update);
router.delete("/lesson/:id", verifyToken, lessonHandler.destroy);

module.exports = router;