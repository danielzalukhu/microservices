const apiAdapter = require('../../apiAdapter');
const {
    URL_SERVICE_MEDIA
} = process.env;

const api = apiAdapter(URL_SERVICE_MEDIA);

module.exports = async (req, res) => {
    try {
        // mengambil id dari parameter url yaitu id 
        const id = req.params.id;
        // memanggil api media dari service media
        const media = await api.delete(`/media/${id}`);
        // media.data ini object dari axios
        return res.json(media.data);
    } catch (error) {

        // buat exception kalau service medianya mati
        if(error.code === 'ECONNREFUSED') {
            return res.status(500).json({ status: 'error', message: 'service unavailable' })
        }

        const { status, data } = error.response;
        return res.status(status).json(data);
    }
} 