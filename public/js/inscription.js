const regexMail = /^[^ ]+@[^ ]+\.[A-z]{2,3}$/
const regexPhone = /^(?:(?:\+|00)33|0)\s*[1-9](?:[\s.-]*\d{2}){4}$/
const regexMdp = /^(?=.*?[a-z])(?=.*?[A-Z])(?=.*?[0-9])(?=.*?[!"#\$%&'\(\)\*\+,-\.\/:;<=>\?@[\]\^_`\{\|}~])[a-zA-Z0-9!"#\$%&'\(\)\*\+,-\.\/:;<=>\?@[\]\^_`\{\|}~]{8,30}$/
function checkField(sender, model) {
    if (!sender.value.match(model)) {
        sender.parentNode.parentNode.classList.add('error')
    }
    else {
        sender.parentNode.parentNode.classList.remove('error')
    }
}

function comparePass(sender, model) {
    model = document.getElementById(model).value
    if (sender.value != model) {
        sender.parentNode.parentNode.classList.add('error')
    } else {
        sender.parentNode.parentNode.classList.remove('error')
    }
}