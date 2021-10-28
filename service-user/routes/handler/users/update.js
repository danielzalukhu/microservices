const { User } = require("../../../models");
const bcrypt = require("bcrypt");
const Validator = require("fastest-validator");
const v = new Validator();

module.exports = async(req, res) => {
    const schema = {
        name: "string|empty:false",
        email: "email|empty:false",
        password: "string|min:6",
        profession: "string|optional",
        avatar: "string|optional",
    };

    const validate = v.validate(req.body, schema);

    if (validate.length) {
        return res.status(400).json({
            status: "error",
            message: validate,
        });
    }

    // 1. Cek id user yg mau di update ini ada atau nggak ambil dari url
    const id = req.params.id;
    const user = await User.findByPk(id);

    if (!user) {
        return res.status(404).json({
            status: "error",
            message: "user not found",
        });
    }

    // 2. Srkg cek emailnya ada gk di database tp ambilnya dari body (inputan)
    const email = req.body.email;
    if (email) {
        const checkEmail = await User.findOne({
            where: { email },
        });
        //  3. Kemudian cek jika email ada di db DAN email tsb tidak sama dengan email sebelumnya
        if (checkEmail && email !== user.email) {
            return res.status(409).json({
                status: "error",
                message: "email already exists",
            });
        }
    }

    // 4. Jika emailnya bukan email lama, passwordnya di encrypt
    const password = await bcrypt.hash(req.body.password, 10);

    // 5. Buat variabel untuk mengambil data dari inputan user
    const { name, profession, avatar } = req.body;

    // 6. Jika validasi sukses, usernya ada, emailnya tidak duplikasi dengan email orang lain
    // Maka update profile mereka
    // usernya ambil dari instance/code di line 26
    await user.update({
        email,
        password,
        name,
        profession,
        avatar,
    });

    // 7. Jika berhasil update maka munculin data baru dengan statusnya
    return res.json({
        status: "success",
        data: {
            id: user.id,
            name,
            email,
            profession,
            avatar,
        },
    });
};