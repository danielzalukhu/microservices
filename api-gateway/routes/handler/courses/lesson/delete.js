const apiAdapter = require("../../../apiAdapter");
const { URL_SERVICE_COURSE } = process.env;

const api = apiAdapter(URL_SERVICE_COURSE);

module.exports = async(req, res) => {
    try {
        // panggil api (endpoin) dari service course yang dibutuhkan
        // kalau handler get panggil route get dsb
        const id = req.params.id;
        const lesson = await api.delete(`api/lesson/${id}`);

        return res.json(lesson.data);
    } catch (error) {
        if (error.code === "ECONNREFUSED") {
            return res.status(500).json({ status: "error", message: "service unavailable" });
        }

        const { status, data } = error.response;
        return res.status(status).json(data);
    }
};