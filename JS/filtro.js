function cepValidoPosse(cepStr) {
  const cep = cepStr.replace(/\D/g, '');
  if (cep.length !== 8) return false;

  function entre(cepNum, inicioStr, fimStr) {
    const inicio = parseInt(inicioStr.replace(/\D/g, ""));
    const fim = parseInt(fimStr.replace(/\D/g, ""));
    return cepNum >= inicio && cepNum <= fim;
  }

  const cepNum = parseInt(cep);

  const faixas = [
    ["73900000", "73900000"],
    ["73908899", "73908899"],
    ["73900001", "73900061"],
    ["73906000", "73906081"],
    ["73906280", "73906334"],
    ["73906340", "73906415"],
    ["73904250", "73904298"],
    ["73904360", "73904465"],
    ["73904150", "73904198"],
    ["73900130", "73900214"],
    ["73902470", "73902473"],
    ["73902490", "73902526"],
    ["73902410", "73902446"],
    ["73904110", "73904146"],
    ["73900070", "73900127"],
    ["73906090", "73906165"],
    ["73904000", "73904105"],
    ["73904210", "73904243"],
    ["73902590", "73902731"],
    ["73902770", "73902884"],
    ["73902180", "73902225"],
    ["73906170", "73906200"],
    ["73906210", "73906273"],
    ["73902000", "73902078"],
    ["73903000", "73903015"],
    ["73900550", "73900970"],
    ["73902450", "73902459"],
    ["73900340", "73900373"],
    ["73904470", "73904476"],
    ["73902480", "73902483"],
    ["73900270", "73900330"],
    ["73900640", "73900691"],
    ["73900430", "73900478"],
  ];

  return faixas.some(([inicio, fim]) => entre(cepNum, inicio, fim));
}

function validarCamposEnderecoModal() {
  const cep = document.getElementById('input-cep-modal').value.replace(/\D/g, '');
  const cidade = document.getElementById('input-cidade-modal').value.trim().toLowerCase();

  const cidadeValida = cidade === 'posse';
  const cepValido = cepValidoPosse(cep);

  if (!cidadeValida) {
    return false;
  }

  if (!cepValido) {
    return false;
  }

  return true;
}


// Feedback visual ao sair do campo CEP
document.getElementById('input-cep-modal').addEventListener('blur', function () {
  const cep = this.value.replace(/\D/g, '');
  const msg = document.getElementById('cep-msg');

  if (cep.length === 8 && !cepValidoPosse(cep)) {
    msg.textContent = `Não fazemos entrega fora da região de Posse.`;
    msg.style.color = 'red';
  } else {
    msg.textContent = '';
  }
});

// Feedback visual ao sair do campo CIDADE
document.getElementById('input-cidade-modal').addEventListener('blur', function () {
  const cidade = this.value.trim().toLowerCase();
  const msgC = document.getElementById('cidade-msg');

  if (cidade === 'posse') {
    msg.textContent = '';
  } else {
    msgC.textContent = `Só atendemos na cidade de Posse`;
    msgC.style.color = 'red';
  }
});