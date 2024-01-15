$(document).ready(() => {

	$('#doc').on('click', () => {
		$('#pagina').load('documentacao.html')
	})

	$('#sup').on('click', () => {
	  $('#pagina').load('suporte.html')
	})

   $('#competencia').on('change', e => {

          let competencia = $(e.target).val()
         
          $.ajax({
          	type: 'GET',
          	url: 'app.php',
          	data: `competencia=${competencia}`,
          	dataType: 'json',
          	success: dados => {
          		$('#numVendas').html(dados.numVendas)
          		$('#totalVendas').html(dados.totalVendas)
          		$('#clienteAtivo').html(dados.clienteAtivo)
          		$('#clienteInativo').html(dados.clienteInativo)
          		},
          	error: erro => {console.log(erro)}
          })
   })

})