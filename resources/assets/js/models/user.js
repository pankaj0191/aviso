export default {
    bootstrap() {
        return axios.get('/api/bootstrap')
            .then(response => {
                return response.data;
            }).catch(error => {
                return {
                    status: 0,
                    errors: error.response.statusText
                };
            });
    },
    userStorage() {
        return axios.get('/api/bootstrap/user-storage')
            .then(response => {
                return response.data;
            }).catch(error => {
                return {
                    status: 0,
                    errors: error.response.statusText
                };
            });
    },
    profile(username) {
        return axios.get(`/api/bootstrap/profile/${username}`)
            .then(response => {
                return response.data;
            }).catch(error => {
                return {
                    status: 0,
                    errors: error.response.statusText
                };
            });
    },
    updateProfileDescription(data) {
        return axios.post(`/api/profile`, data)
            .then(response => {
                return response.data;
            }).catch(error => {
                return {
                    status: 0,
                    errors: error.response.statusText
                };
            });
    },
    updateHourlyRate(data) {
        return axios.post(`/api/profile/hourly-rate`, data)
            .then(response => {
                return response.data;
            }).catch(error => {
                return {
                    status: 0,
                    errors: error.response.statusText
                };
            });
    },
    updateNotificationReview(data) {
        return axios.post(`/api/profile/update-notify`, data)
            .then(response => {
                return response.data;
            }).catch(error => {
                return {
                    status: 0,
                    errors: error.response.statusText
                };
            });
    },
    changeProjectsListingType(type) {
        return axios.put('/api/bootstrap/change-projects-listing-type', { type: type })
            .then(response => {
                return response.data;
            })
            .catch(error => {
                return {
                    status: 0,
                    errors: error.response.statusText
                };
            });
    },
    getRecentData() {
        return axios.get('/api/bootstrap/get-recent-datas')
            .then(response => {
                return response.data;
            }).catch(error => {
                return {
                    status: 0,
                    errors: error.response.statusText
                };
            });
    },
    getActiveSubscription() {
        return axios.get('/api/bootstrap/active-subscription')
            .then(response => {
                return response.data;
            })
            .catch(error => {
                return {
                    status: 0,
                    errors: error.response.statusText
                };
            });
    },
    getPaymentMethods(user) {
        return axios.get(`/api/payments/get-payment-methods/${user.stripe_id}`)
            .then(response => {
                return response.data;
            })
            .catch(error => {
                return {
                    status: 0,
                    errors: error.response.statusText
                };
            });
    },
}