const apiAdapter = require("../../apiAdapter");
const { URL_SERVICE_USER } = process.env;

const api = apiAdapter(URL_SERVICE_USER);

module.exports = async(req, res) => {
    try {
        /*
         * Kita perlu ambil ID user yang login dalam token yang diinject dari middleware ketika login
         * (lihat di folder middleware verifyToken) --> req.user
         */

        // return res.json(req.user);
        const id = req.user.data.id;
        const user = await api.get(`/users/${id}`);

        return res.json(user.data);
    } catch (error) {
        if (error.code === "ECONNREFUSED") {
            return res.status(500).json({ status: "error", message: "service unavailable" });
        }

        const { status, data } = error.response;
        return res.status(status).json(data);
    }
};