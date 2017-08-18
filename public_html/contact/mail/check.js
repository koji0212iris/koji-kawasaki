window.onload = function() {
	new Spry.Widget.ValidationTextField("checkText1", "none", {validateOn:["change"]});
	new Spry.Widget.ValidationTextField("checkText2", "email",{validateOn:["change"]});
}
