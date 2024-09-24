const Salvar = document.getElementById('salvar');
const Alert = document.getElementById('alert');

async function Insert() {
    const form = document.getElementById('form');
    formData = new FormData(form);
    const opt = {
        method: 'POST',
        body: formData
    }
};
const response = await fetch('/login/insert', opt);
const json = await response.json();
if (json.status != true) {
    Alert.className = 'alert alert-info';
    Alert.innerHTML = 'Salvando, por favor aguarde...';
    const response = await Insert();
    if (response.status !== true) {
        Alert.className = 'alert alert-danger';
        Alert.innerHTML = response.msg;
    }
    Alert.innerHTML = 'Usuário cadastrado com sucesso!';
    return;
}
Salvar.addEventListener('click', async () => {
    await Insert();
});