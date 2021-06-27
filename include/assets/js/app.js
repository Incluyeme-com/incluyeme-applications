window.onload = function () {
    Vue.component('vueApplications1', {
        template: '#vueApplications1',
    });
    const vueApplications = new Vue({
        el: '#vueApplications',
        data: {
            step: 1,
            url: null,
            emailCan: null,
            name: null,
            keyWord: null,
            candidatesInformation: [],
        },
        methods: {
            changeScreens(step) {
                switch (step) {
                    case 1:
                        this.step = step
                        break
                    case 2:
                        this.step = step
                        break
                    case 3:
                        this.step = step
                        break
                    case 4:
                        this.step = step
                        break
                    case 5:
                        this.step = step
                        break
                }
            },
            searchCandidate: async function (url, button) {
                switch (button) {
                    case 1:
                        this.name = null;
                        this.keyWord = null;
                        break;
                    case 2:
                        this.emailCan = null;
                        this.keyWord = null;
                        break;
                    case 3:
                        this.emailCan = null;
                        this.name = null;
                        break;
                }
                this.url = url;
                const verifications = await axios.post(this.url + '/incluyeme-applications/include/verifications.php',
                    {
                        email: this.emailCan,
                        name: this.name,
                        keyword: this.keyWord,
                        candidateSearch: true
                    })
                    .then(function (response) {
                        return response
                    })
                    .catch(function (error) {
                        return true;
                    });
                this.candidatesInformation = verifications.data.message;
                console.log(verifications)
                this.changeScreens(2)
            },
            openUrl(url) {
                console.log(url)
                window.open(url);
                return false;
            },
        }
    })
}