import axios from 'axios';

// axios is already configured in bootstrap.ts with:
// - withCredentials: true
// - X-Requested-With: XMLHttpRequest
// - withXSRFToken: true

export const api = {
    get: (url: string) => axios.get(url),
    post: (url: string, data?: unknown) => axios.post(url, data),
    put: (url: string, data?: unknown) => axios.put(url, data),
    delete: (url: string) => axios.delete(url),
};

export default api;
