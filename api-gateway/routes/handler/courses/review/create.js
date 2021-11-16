const apiAdapter = require("../../../apiAdapter");
const { URL_SERVICE_COURSE } = process.env;

const api = apiAdapter(URL_SERVICE_COURSE);

module.exports = async(req, res) => {
    try {
        /*
         * Kita perlu ambil ID user yang login dalam token yang diinject dari middleware ketika login
         * (lihat di folder middleware verifyToken) --> req.user
         */
        const user_id = req.user.data.id;

        // panggil api (endpoin) dari service course yang dibutuhkan
        // kalau handler get panggil route get dsb
        const review = await api.post("api/review", {
            user_id,
            ...req.body,
        });

        return res.json(review.data);
    } catch (error) {
        if (error.code === "ECONNREFUSED") {
            return res.status(500).json({ status: "error", message: "service unavailable" });
        }

        const { status, data } = error.response;
        return res.status(status).json(data);
    }
};