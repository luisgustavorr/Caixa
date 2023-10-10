let ja_clicado = false
let codigo_rodando = false
let elemento_alvo = ''
$(".select_valor_clicker_father").click(function(){
  if( $(this).attr("style") == 'justify-content: end;'){
    $("."+$(this).attr("button_identifier")+"_button").val(1);

    $(this).css('justify-content','start')
    $(this).attr("medida","un")
    $(this).children().text('UN')

  }else{
    if($(this).attr("style") == '' && !codigo_rodando){
      codigo_rodando = true
      
      connectToSerial()

    }
    elemento_alvo = $(this)
    $(this).css('justify-content','end')
    $(this).children().text('KG')
    $(this).attr("medida","kg")

  }

})

  async function connectToSerial() {

   

   
    try {
      const port = await navigator.serial.requestPort();
      console.log(port);
  
      if (!port.readable) {
        await port.open({ baudRate: 4800 });
      }
  
      const reader = port.readable.getReader();
  
      let dataPacket = []; // Armazena os dados recebidos da balança
 
      while (true) {
       console.log("rodando")
        
        const { value, done } = await reader.read();
        if (done) {
          // |reader| has been canceled.
          console.log('parou')
          break;
        }
  
        // Adicione os dados recebidos à lista de dados
        dataPacket.push(...value);
  
        // Verifique se o pacote de dados foi completamente recebido

        if (dataPacket.includes(0x0D)) {
          // O caractere CR (0DH) indica o final do pacote
          processReceivedData(dataPacket,elemento_alvo);
          dataPacket = []; // Limpa o pacote para o próximo
          
        }
      }
    } catch (error) {
      // Handle |error|…
      console.log(error);
    }
    
  
  }
  
  function processReceivedData(data,esse) {
    // Processa os dados recebidos da balança
    console.log("Dados recebidos:", data);
  
    // Extrai o peso da sequência de dados
    const weightStartIndex = data.indexOf(0x02) + 1;
    console.log(weightStartIndex)
    const weightEndIndex = data.indexOf(0x0D);
    if (weightStartIndex >= 0 && weightEndIndex > weightStartIndex) {
      const weightData = data.slice(weightStartIndex, weightEndIndex).map(byte => String.fromCharCode(byte)).join('');
      if($(esse).attr("medida") != 'un'){
        
        $("."+$(esse).attr("button_identifier")+"_button").val(weightData);

      }
    } else {
      console.log("Pacote de dados incompleto ou inválido.");
    }
  }
  