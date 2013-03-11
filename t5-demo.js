var submitButton = document.getElementById( "submit" );

if ( submitButton.addEventListener ) {
	submitButton.addEventListener( "click", confirmClick, false );
} else {
	submitButton.attachEvent( "onclick", confirmClick );
}

function confirmClick() {
	alert( "Clicked!" );
};