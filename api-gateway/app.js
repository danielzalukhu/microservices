require("dotenv").config();
const express = require("express");
const path = require("path");
const cookieParser = require("cookie-parser");
const logger = require("morgan");

const indexRouter = require("./routes/index");
const mediaRouter = require("./routes/media");
const usersRouter = require("./routes/users");
const refreshTokensRouter = require("./routes/refreshTokens");
const coursesRouter = require("./routes/courses");
const ordersRouter = require("./routes/orders");
const paymentsRouter = require("./routes/payments");

const verifyToken = require("./middleware/verifyToken");

const app = express();

app.use(logger("dev"));
app.use(express.json({ limit: "50mb" }));
app.use(express.urlencoded({ extended: false, limit: "50mb" }));
app.use(cookieParser());
app.use(express.static(path.join(__dirname, "public")));

app.use("/", indexRouter);
app.use("/media", mediaRouter);
app.use("/users", usersRouter);
app.use("/refresh_tokens", refreshTokensRouter);
app.use("/courses", coursesRouter);

module.exports = app;