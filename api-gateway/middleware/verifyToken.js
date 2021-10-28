const jwt = require("jsonwebtoken");
const { JWT_SECRET } = process.env;

module.exports = async(req, res, next) => {
    const token = req.headers.authorization;

    jwt.verify(token, JWT_SECRET, (err, decoded) => {
        if (err) return res.status(403).json({ message: err.message });

        // Kalau berhasil/token = valid, maka injcet data yang ter-decode dari token ke dalam
        //  object user. Objct user yang adalah user dari login sebagai key authorization pada endpoint lain.
        req.user = decoded;
        return next();
    });
};