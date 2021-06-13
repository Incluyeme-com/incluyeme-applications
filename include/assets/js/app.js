window.onload = function () {
    let app = new Vue({
        el: '#vueApp',
        data: {
            step: 1
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
            }
        }
    })
}