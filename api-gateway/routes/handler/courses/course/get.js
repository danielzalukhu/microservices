const apiAdapter = require("../../../apiAdapter");
const { URL_SERVICE_COURSE, URL_API_GATEWAY } = process.env;

const api = apiAdapter(URL_SERVICE_COURSE);

module.exports = async(req, res) => {
    try {
        // panggil api (endpoin) dari service course yang dibutuhkan
        // kalau handler get panggil route get dsb
        const course = await api.get("api/course", {
            /* we can custom params as an attribute to show only selected fields */
            params: {
                ...req.query,
                // status: "draft",
            },
        });

        const coursesData = course.data;
        const firstPage = coursesData.data.first_page_url.split("?").pop();
        const lastPage = coursesData.data.last_page_url.split("?").pop();

        coursesData.data.first_page_url = `${URL_API_GATEWAY}/courses/catalog?${firstPage}`;
        coursesData.data.last_page_url = `${URL_API_GATEWAY}/courses/catalog?${lastPage}`;

        if (coursesData.data.next_page_url) {
            const nextPage = coursesData.data.next_page_url.split("?").pop();
            coursesData.data.next_page_url = `${URL_API_GATEWAY}/courses/catalog?${nextPage}`;
        }

        if (coursesData.data.prev_page_url) {
            const prevPage = coursesData.data.prev_page_url.split("?").pop();
            coursesData.data.prev_page_url = `${URL_API_GATEWAY}/courses/catalog?${prevPage}`;
        }

        coursesData.data.path = `${URL_API_GATEWAY}/courses/catalog`;
        // console.log(coursesData.data.path);

        return res.json(coursesData);
    } catch (error) {
        if (error.code === "ECONNREFUSED") {
            return res.status(500).json({ status: "error", message: "service unavailable" });
        }

        const { status, data } = error.response;
        return res.status(status).json(data);
    }
};