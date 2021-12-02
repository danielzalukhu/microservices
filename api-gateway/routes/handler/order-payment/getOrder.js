const apiAdapter = require("../../apiAdapter");
const { URL_SERVICE_ORDER_PAYMENT } = process.env;

const api = apiAdapter(URL_SERVICE_ORDER_PAYMENT);

module.exports = async(req, res) => {
    try {
        /*
         * Kita perlu ambil ID user yang login dalam token yang diinject dari middleware ketika login
         * (lihat di folder middleware verifyToken) --> req.user
         */
        const user_id = req.user.data.id;

        const order = await api.get("/api/order", {
            params: { user_id },
        });

        return res.json(order.data);
    } catch (error) {
        if (error.code === "ECONNREFUSED") {
            return res.status(500).json({ status: "error", message: "service unavailable" });
        }

        const { status, data } = error.response;
        return res.status(status).json(data);
    }
};