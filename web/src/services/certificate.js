import api from './api';

export const certificateService = {
    // Get all certificates
    getAll(params = {}) {
        return api.get('/certificates', { params });
    },

    // Get my certificates
    getMyCertificates() {
        return api.get('/my-certificates');
    },

    // Get single certificate
    getById(id) {
        return api.get(`/certificates/${id}`);
    },

    // Create new certificate
    create(data) {
        return api.post('/certificates', data, {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        });
    },

    // Update certificate - FIX: gunakan PUT/POST dengan _method
    update(id, data) {
        // Opsi 1: Gunakan PUT method
        return api.post(`/certificates/${id}`, data, {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        });
        
        // Atau Opsi 2: Jika backend expect PUT
        // return api.put(`/certificates/${id}`, data, {
        //     headers: {
        //         'Content-Type': 'multipart/form-data'
        //     }
        // });
    },

    // Delete certificate
    delete(id) {
        return api.delete(`/certificates/${id}`);
    },

    // Download certificate file - FIX: parameter
    downloadFile(id) {
        return api.get(`/certificates/${id}/download`, {
            responseType: 'blob'
        });
    },

    // Optional: Preview certificate file
    previewFile(id) {
        return api.get(`/certificates/${id}/preview`);
    }
};