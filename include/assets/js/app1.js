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
            employeeInformation: [],
            company: null,
            job: null,
            jobId: null,
            applicants: new Map(),
            jobs: new Map(),
            textApplication: null,
            textEmail: null,
            resultsNumbers: 1,
            resultsNumbersEmployee: 1,
            buttonAplicant: null,
            buttonEmplotee: null
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
                    case 6:
                        this.step = step
                        break
                }
            },
            searchCandidate: async function (url, button, page) {
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
                this.buttonAplicant = button;
                this.resultsNumbers = page ? this.resultsNumbers + page : this.resultsNumbers
                const verifications = await axios.post(this.url + '/incluyeme-applications/include/verifications.php',
                    {
                        email: this.emailCan,
                        name: this.name,
                        keyword: this.keyWord,
                        candidateSearch: true,
                        resultsNumbers: this.resultsNumbers
                    })
                    .then(function (response) {
                        return response
                    })
                    .catch(function (error) {
                        return true;
                    });
                this.candidatesInformation = verifications.data.message;
                this.changeScreens(2)
            },
            searchEmployee: async function (button, page) {
                switch (button) {
                    case 1:
                        this.company = null;
                        this.jobId = null;
                        break;
                    case 2:
                        this.jobId = null;
                        this.job = null;
                        break;
                    case 3:
                        this.company = null;
                        this.job = null;
                        break;
                    case 4:
                        this.company = null;
                        this.job = null;
                        this.jobId = null;
                        break;
                }
                this.buttonAplicant = button;
                  console.log(this.resultsNumbersEmployee)
                this.resultsNumbersEmployee = page ? this.resultsNumbersEmployee + page : this.resultsNumbersEmployee;
                console.log(page)
                const verifications = await axios.post(this.url + '/incluyeme-applications/include/verifications.php',
                    {
                        job: this.job,
                        company: this.company,
                        jobId: this.jobId,
                        employerSearch: true,
                        resultsNumbers: this.resultsNumbersEmployee
                    })
                    .then(function (response) {
                        return response
                    })
                    .catch(function (error) {
                        return true;
                    });
                this.employeeInformation = verifications.data.message;
                this.changeScreens(4)
            },
            searchMore: async function (type, page) {
                document.body.scrollTop = document.documentElement.scrollTop = 0;
                switch (type) {
                    case 1:
                        await this.searchCandidate(this.url, this.buttonAplicant, page)
                        break
                    case 2:
                        await this.searchEmployee(this.buttonEmplotee, page)
                        break
                }
            },
            addCandidate(id) {
                if (this.applicants.get(id)) {
                    this.applicants.delete(id);
                } else {
                    this.applicants.set(id, id);
                }
            },
            addJob(id) {
                if (this.jobs.get(id)) {
                    this.jobs.delete(id);
                } else {
                    this.jobs.set(id, id);
                }
            },
            openUrl(url) {
                console.log(url)
                window.open(url);
                return false;
            },
            appApplications: async function () {
                const verifications = await axios.post(this.url + '/incluyeme-applications/include/verifications.php',
                    {
                        applicants: Array.from(this.applicants.keys()),
                        jobs: Array.from(this.jobs.keys()),
                        appApplications: true,
                        textApplication: this.textApplication,
                        textEmail: this.textEmail,
                    })
                    .then(function (response) {
                        return response
                    })
                    .catch(function (error) {
                        return true;
                    });
                console.log(verifications)
                this.changeScreens(6)
            },
            reloadAll: function () {
                window.location.reload()
            }
        }
    })
}