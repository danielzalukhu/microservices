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
        const my_course = await api.get("api/my_course", {
            params: { user_id },
        });

        return res.json(my_course.data);
    } catch (error) {
        if (error.code === "ECONNREFUSED") {
            return res.status(500).json({ status: "error", message: "service unavailable" });
        }

        const { status, data } = error.response;
        return res.status(status).json(data);
    }
};