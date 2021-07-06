let adresse = window.location.href;

//**********************************Traitement

/**********************************RESTART DES VALEURS**********************/

$('#clearForm').on('click',function(e)
{
  $('#formSearchInner input[type!="submit"]').each(function(e)
  {
    $(this).val("");
  });

  $('#formSearchInner select').each(function(e)
  {
    $(this).val("");
  });
  
  verifSearchFrom();
  $('#formSearchInner').submit();
});

/**********************MISE EN PLACE DES VALEURS***************************/
$('#formSearchInner').attr('action', window.location );

$('#formSearchInner #searchValue').val(getValue("searchValue"));

if (adresse.indexOf("asc") != -1)
{
  $('#triProduct option[value="asc"]').attr('selected', 'selected');
}
else if (adresse.indexOf("desc") != -1)
{
  $('#triProduct option[value="desc"]').attr('selected', 'selected');
}
else if (adresse.indexOf("alpha") != -1)
{
  $('#triProduct option[value="alpha"]').attr('selected', 'selected');
}

//Vérification du contenu si vide, on retire du formulaire
$('#formSearchInner input[type="submit"]').on('click', function(e)
{
  verifSearchFrom(); 
});

$('#triProduct').on('change', function (e) {
  verifSearchFrom();
  $('#formSearchInner').submit();
});
/******************************FUNCTION*************************************/
function verifSearchFrom()
{
  $('#formSearchInner input').each(function(e)
  {
    if($(this).val() == "")
    {
      $(this).removeAttr('name');
    }
  });
  $('#formSearchInner select').each(function(e)
  {
    if($(this).val() == "")
    {
      $(this).removeAttr('name');
    }
  });
}

function getValue(param) {
	var vars = {};
	window.location.href.replace( location.hash, '' ).replace( 
		/[?&]+([^=&]+)=?([^&]*)?/gi, // regexp
		function( m, key, value ) { // callback
			vars[key] = value !== undefined ? value : '';
		}
	);

	if ( param ) {
		return vars[param] ? vars[param] : null;	
	}
	return vars;
}