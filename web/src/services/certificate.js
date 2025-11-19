import api from './api';

export const certificateService = {
    // Get all certificates
    getAll(params = {}) {
        return api.get('/certificates', { params });
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

    // Update certificate
    update(id, data) {
        return api.post(`/certificates/${id}`, data, {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        });
    },

    // Delete certificate
    delete(id) {
        return api.delete(`/certificates/${id}`);
    },

    // ✅ FIXED: Download certificate file
    async download(certificate) {
        try {
            const response = await api.get(`/certificates/${certificate.id}/download`, {
                responseType: 'blob'
            });

            // Create blob link to download
            const url = window.URL.createObjectURL(new Blob([response.data]));
            const link = document.createElement('a');
            link.href = url;
            
            // Get filename from header or use default
            let filename = certificate.file_name || `${certificate.certificate_number}.pdf`;
            const contentDisposition = response.headers['content-disposition'];
            if (contentDisposition) {
                const filenameMatch = contentDisposition.match(/filename="(.+)"/);
                if (filenameMatch && filenameMatch.length === 2) {
                    filename = filenameMatch[1];
                }
            }
            
            link.setAttribute('download', filename);
            document.body.appendChild(link);
            link.click();
            link.remove();
            window.URL.revokeObjectURL(url);

            return { success: true, message: 'File downloaded successfully' };
        } catch (error) {
            console.error('Download error:', error);
            throw error;
        }
    },

    // View certificate file in browser
    view(id) {
        const baseURL = api.defaults.baseURL || '';
        window.open(`${baseURL}/certificates/${id}/view`, '_blank');
    },

    // Get statistics
    getStatistics() {
        return api.get('/certificates/statistics');
    }
};