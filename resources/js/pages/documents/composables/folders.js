import { ref } from 'vue'
import axios from 'axios'

export default function useFolders() {
    const errors = ref('')
    const folder = ref({});
    const selectedFolder = ref({});
    const folders = ref([]);

    const foldersPaginationData = ref({
        current_page: 1,
        data: [],
        last_page: 1,
    });

    const foldersList = ref([]);
    const foldersListCurrentPage = ref(1);
    const foldersListLastPage = ref(1);

    const file = ref(null);
    const sourceFile = ref(null);
    const filesList = ref([]);
    const filesListCurrentPage = ref(1);
    const filesListTotal = ref(0);
    const filesListLastPage = ref(1);

    const breadcrumbs = ref([]);

    const resetFileList = () => {
        foldersListCurrentPage.value = 1;
        foldersListLastPage.value = 1;
        foldersList.value = [];
    }

    const property = ref({});


    const getFolder = async (slug) => {
        breadcrumbs.value = [];
        resetFileList();
        if(!slug){
            folder.value = [];
            return;
        }

        let response = await axios.get(`/api/vue/v1/get-folder`, {
            params: {
                slug
            }
        })
        folder.value = response.data.data
        breadcrumbs.value = response.data.breadcrumbs
    }

    const getFolders = async () => {
        let response = await axios.get(`/api/vue/v1/get-folders`)
        folders.value = response.data.data
    }

    const getFoldersList = async (page = 1, id = null, reset = false, root = true, term = "") => {
        let oldData = foldersList.value;
        let response = await axios.get(`/api/vue/v1/get-folders-list`, {
            params: {
                page,
                id,
                root,
                term
            }
        })

        const { current_page, data, last_page } = response.data.data;
        foldersListCurrentPage.value = current_page;
        foldersListLastPage.value = last_page;
        if (reset) {
            oldData = data;
        } else {
            data.map(item => {
                const oldDataIndex = oldData.findIndex(oldItem => oldItem.id === item.id);
                if (oldDataIndex >= 0) {
                    oldData[oldDataIndex] = item;
                } else {
                    oldData.push(item);
                }
                return item;
            })
        }
        foldersList.value = oldData;
    }

    const storeFolder = async (data) => {
        errors.value = ''
        const oldData = foldersList.value;
        try {
            const formData = new FormData();
            for (const key in data) {
                formData.append(key, data[key]);
            }
            const config = {
                headers: {
                    'Content-type': 'multipart/form-data'
                }
            }
            let response = await axios.post('/api/vue/v1/store-folder', formData, config).then(function (response) {
                if (response.data.status == '1') {
                    oldData.splice(0, 0, response.data.data);
                    foldersList.value = oldData;
                    showToastify(response.data.msg, 'success');
                    return response;
                } else {
                    showToastify(response.data.msg, 'error')
                    return response;
                }
            }).catch(function () {
                return response;
            });

            return response;
        } catch (e) {
            if (e.response.status === 422) {
                for (const key in e.response.data.errors) {
                    errors.value = e.response.data.errors
                }
            }
            throw e;
        }
    }

    const updateFolder = async (data) => {
        errors.value = ''
        const oldData = foldersList.value;
        try {
            const formData = new FormData();
            for (const key in data) {
                formData.append(key, data[key]);
            }
            const config = {
                headers: {
                    'Content-type': 'multipart/form-data'
                }
            }
            let response = await axios.post('/api/vue/v1/update-folder', formData, config).then(function (response) {
                if (response.data.status == '1') {
                    const oldDataIndex = oldData.findIndex(f => f.id === data.id);
                    oldData[oldDataIndex] = response.data.data;
                    foldersList.value = oldData;
                    showToastify(response.data.msg, 'success');
                    return response;
                } else {
                    showToastify(response.data.msg, 'error')
                    return response;
                }
            }).catch(function () {
                return response;
            });

            return response;
        } catch (e) {
            if (e.response.status === 422) {
                for (const key in e.response.data.errors) {
                    errors.value = e.response.data.errors
                }
            }
            throw e;
        }
    }

    const deleteFolder = async (id) => {
        errors.value = ''
        const oldData = foldersList.value;
        try {
            let response = axios.delete(`/api/vue/v1/delete-folder/${id}`).then(function (response) {
                if (response.data.status == '1') {
                    const oldDataIndex = oldData.findIndex(f => f.id === id);
                    oldData.splice(oldDataIndex, 1);
                    foldersList.value = oldData;
                    showToastify(response.data.msg, 'success');
                    return response
                } else {
                    showToastify(response.data.msg, 'error')
                    return response
                }
            }).catch(function () {
                return response;
            });

            return response;
        } catch (e) {
            if (e.response.status === 422) {
                for (const key in e.response.data.errors) {
                    errors.value = e.response.data.errors
                }
            }
            throw e;
        }
    }

    const downloadFolderZip = async (folderId) => {
        errors.value = ''

        try {
            const formData = new FormData();
            formData.append('id', folderId);

            let response = await axios.post('/api/vue/v1/download-folder-zip', formData).then(function (response) {
                if (response.data.status == '1') {
                    showToastify(response.data.msg, 'success');
                    return response;
                } else {
                    showToastify(response.data.msg, 'error')
                    return response;
                }
            }).catch(function () {
                return response;
            });

            return response;
        } catch (e) {
            if (e.response.status === 422) {
                for (const key in e.response.data.errors) {
                    errors.value = e.response.data.errors
                }
            }
            throw e;
        }
    }

    const uploadFiles = async (files, folderId) => {
        errors.value = ''

        try {
            const formData = new FormData();
            formData.append('folder_id', folderId);
            files.forEach((file, index) => {
                formData.append(`files[${index}]`, file);
            });

            const config = {
                headers: {
                    'Content-type': 'multipart/form-data'
                }
            }
            let response = await axios.post('/api/vue/v1/upload-files', formData, config).then(function (response) {
                if (response.data.status == '1') {
                    showToastify(response.data.msg, 'success');
                    return response;
                } else {
                    showToastify(response.data.msg, 'error')
                    return response;
                }
            }).catch(function () {
                return response;
            });

            return response;
        } catch (e) {
            if (e.response.status === 422) {
                for (const key in e.response.data.errors) {
                    errors.value = e.response.data.errors
                }
            }
            throw e;
        }
    }

    const getFilesList = async (page = 1, folderId = null) => {
        let response = await axios.get(`/api/vue/v1/get-files-list`, {
            params: {
                page,
                folder_id: folderId
            }
        })

        const { current_page, data, last_page, total } = response.data.data;
        filesListCurrentPage.value = current_page;
        filesListLastPage.value = last_page;
        filesListTotal.value = total;
        filesList.value = data;
    }

    const deleteFile = async (id) => {
        errors.value = ''
        try {
            let response = axios.delete(`/api/vue/v1/delete-file/${id}`).then(function (response) {
                if (response.data.status == '1') {
                    showToastify(response.data.msg, 'success');
                    return response
                } else {
                    showToastify(response.data.msg, 'error')
                    return response
                }
            }).catch(function () {
                return response;
            });

            return response;
        } catch (e) {
            if (e.response.status === 422) {
                for (const key in e.response.data.errors) {
                    errors.value = e.response.data.errors
                }
            }
            throw e;
        }
    }
    const generatePDF = async (data) => {
        errors.value = '';

        try {
            const formData = new FormData();
            for (const key in data) {
                formData.append(key, data[key]);
            }
            const config = {
                headers: {
                    'Content-type': 'multipart/form-data'
                }
            };

            const response = await axios.post('/api/vue/v1/generate-pdf', formData, config);

            if (response.data.status == '1') {
                showToastify(response.data.msg, 'success');
            } else {
                showToastify(response.data.msg, 'error');
            }

            return response;
        } catch (error) {
          //  console.error(error);
            throw error; // Re-throw the error for further handling if needed
        }
    };





    // const generatePDF = async (data) => {
    //     errors.value = '';
    //     try {
    //         const formData = new FormData();
    //         for (const key in data) {
    //             formData.append(key, data[key]);
    //         }
    //         const config = {
    //             headers: {
    //                 'Content-type': 'multipart/form-data'
    //             }
    //         }
    //         let response = await axios.post('/api/vue/v1/generate-pdf', formData, config).then(function (response) {
    //             if (response.data.status == '1') {
    //                 showToastify(response.data.msg, 'success');
    //                 return response;
    //             } else {
    //                 showToastify(response.data.msg, 'error')
    //                 return response;
    //             }
    //         }).catch(function () {
    //             return response;
    //         });
    //
    //         return response;
    //     } catch (e) {
    //         console.log(e.response.data.errors)
    //         if (e.response.status === 422) {
    //             for (const key in e.response.data.errors) {
    //                 errors.value = e.response.data.errors
    //             }
    //         }
    //         throw e;
    //     }
    // }

    const getProperty = async (id, ref_no = null) => {
        property.value = {};

        if((id === null || id === undefined) && (ref_no === null || ref_no === undefined)){
            return;
        }

        let response = await axios.get(`/api/vue/v1/get-property`, {
            params: {
                id,
                ref_no
            }
        })
        property.value = response.data.data;
    }

    const getFile = async (id, slug = null,type = 'file') => {
        file.value = null;
        sourceFile.value = null;

        if((id === null || id === undefined) && (slug === null || slug === undefined)){
            return;
        }
        let response = await axios.get(`/api/vue/v1/get-file`, {
            params: {
                id,
                slug
            }
        })
        if(type === 'file') {
            file.value = response.data.data;
        }else{
            sourceFile.value = response.data.data;
        }
        return response
    }

    // Handle all the non-2xx responses
    axios.interceptors.response.use(response => {
        return response;
    }, error => {
        if (error.response && error.response.status === 422) {
            const errorData = error.response.data.errors;
            for (const key in errorData) {
                if (errorData.hasOwnProperty(key)) {
                    errorData[key].forEach(errorMessage => {
                        showToastify(errorMessage, 'error');
                        errors.value += `${errorMessage}\n`;
                    });
                }
            }
        }
        // throw error;
    });

    return {
        errors,
        selectedFolder,
        folders,
        foldersPaginationData,
        foldersList,
        foldersListCurrentPage,
        foldersListLastPage,
        folder,
        storeFolder,
        updateFolder,
        deleteFolder,
        getFolder,
        getFolders,
        getFoldersList,
        downloadFolderZip,

        sourceFile,
        file,
        getFile,
        filesList,
        filesListCurrentPage,
        filesListLastPage,
        filesListTotal,
        uploadFiles,
        getFilesList,
        deleteFile,

        breadcrumbs,

        generatePDF,

        property,
        getProperty
    }
}
