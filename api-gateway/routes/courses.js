const express = require("express");
const router = express.Router();
const verifyToken = require("../middleware/verifyToken");
const permission = require("../middleware/permission");
const mentorHandler = require("./handler/courses/mentor");
const courseHandler = require("./handler/courses/course");
const chapterHandler = require("./handler/courses/chapter");
const lessonHandler = require("./handler/courses/lesson");
const imageCourseHandler = require("./handler/courses/image_course");
const myCourseHandler = require("./handler/courses/my_course");
const reviewHandler = require("./handler/courses/review");

/* Mentor route listing. */
router.get("/mentor/", verifyToken, permission("admin"), mentorHandler.read);
router.post("/mentor/", verifyToken, permission("admin"), mentorHandler.create);
router.get("/mentor/:id", verifyToken, permission("admin"), mentorHandler.show);
router.put("/mentor/:id", verifyToken, permission("admin"), mentorHandler.update);
router.delete("/mentor/:id", verifyToken, permission("admin"), mentorHandler.destroy);

/* Course route listing. */
router.get("/catalog", courseHandler.get);
router.post("/catalog", verifyToken, permission("admin"), courseHandler.create);
router.get("/catalog/:id", courseHandler.show);
router.put("/catalog/:id", verifyToken, permission("admin"), courseHandler.update);
router.delete("/catalog/:id", verifyToken, permission("admin"), courseHandler.destroy);

/* Chapter route listing. */
router.get("/chapter", verifyToken, permission("admin"), chapterHandler.get);
router.post("/chapter", verifyToken, permission("admin"), chapterHandler.create);
router.get("/chapter/:id", verifyToken, permission("admin"), chapterHandler.show);
router.put("/chapter/:id", verifyToken, permission("admin"), chapterHandler.update);
router.delete("/chapter/:id", verifyToken, permission("admin"), chapterHandler.destroy);

/* Lesson route listing. */
router.get("/lesson", verifyToken, permission("admin"), lessonHandler.get);
router.post("/lesson", verifyToken, permission("admin"), lessonHandler.create);
router.get("/lesson/:id", verifyToken, permission("admin"), lessonHandler.show);
router.put("/lesson/:id", verifyToken, permission("admin"), lessonHandler.update);
router.delete("/lesson/:id", verifyToken, permission("admin"), lessonHandler.destroy);

/* ImageCourse route listing. */
router.post("/image_course", verifyToken, permission("admin"), imageCourseHandler.create);
router.delete("/image_course/:id", verifyToken, permission("admin"), imageCourseHandler.destroy);

/* MyCourse route listing. */
router.get("/my_course", verifyToken, permission("admin", "student"), myCourseHandler.get);
router.post("/my_course", verifyToken, permission("admin", "student"), myCourseHandler.create);

/** Review route listing */
router.post("/review", verifyToken, permission("admin", "student"), reviewHandler.create);
router.put("/review/:id", verifyToken, permission("admin", "student"), reviewHandler.update);
router.delete("/review/:id", verifyToken, permission("admin", "student"), reviewHandler.destroy);

module.exports = router;