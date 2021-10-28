const jwt = require("jsonwebtoken");
const apiAdapter = require("../../apiAdapter");
const { URL_SERVICE_USER, JWT_SECRET, JWT_SECRET_REFRESH_TOKEN, JWT_ACCESS_TOKEN_EXPIRED } = process.env;

const api = apiAdapter(URL_SERVICE_USER);

module.exports = async(req, res) => {
    try {
        const refreshToken = req.body.refresh_token;
        const email = req.body.email;

        if (!refreshToken || !email) {
            return res.status(400).json({
                status: "error",
                message: "invalid token",
            });
        }

        // cek apakah refresh token ini ada apa tidak di database
        // kita perlu butuh supply service user untuk api get token
        await api.get("/refresh_tokens", { params: { refresh_token: refreshToken } });

        // kalau refresh token ada di database kita validasi tokennya
        jwt.verify(refreshToken, JWT_SECRET_REFRESH_TOKEN, (err, decoded) => {
            if (err) {
                return res.status(403).json({
                    status: "error",
                    message: err.message,
                });
            }

            // selain token yang divalidasi, kita validasi user login dengan emailnya
            // sehingga antara user email yg pnya id itu harus cocok dengan refresh tokennya
            // sesuai dengan yg tersimpan di database
            if (email !== decoded.data.email) {
                return res.status(400).json({
                    status: "error",
                    message: "token is exists but email is not valid",
                });
            }

            // kemudian kalau email dan refresh_token divalidasi dan cocok maka
            // kita generate token baru lalu kirim ke front end untuk digunain lagi
            const token = jwt.sign({ data: decoded.data }, JWT_SECRET, { expiresIn: JWT_ACCESS_TOKEN_EXPIRED });
            return res.json({
                status: "success",
                data: token,
            });
        });
    } catch (error) {
        if (error.code === "ECONNREFUSED") {
            return res.status(500).json({ status: "error", message: "service unavailable" });
        }

        const { status, data } = error.response;
        return res.status(status).json(data);
    }
};