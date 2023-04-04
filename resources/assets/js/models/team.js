export default {
    teams() {
        return axios.get('/api/my-users/teams').then(response => {
            return response.data;
        }).catch(error => {
            return error.response;
        });
    }
}