// XMLHttpRequest-related boilerplate.
var xhr = createRequest();

function createRequest() {
    var xhr = false;  
    if (window.XMLHttpRequest) {
        xhr = new XMLHttpRequest();
    }
    else if (window.ActiveXObject) {
        xhr = new ActiveXObject("Microsoft.XMLHTTP");
    }
    return xhr;
} // end function createRequest()

// Sends english + spanish translation data to the database
// Takes input data from the index.html form, 
// perform clientside validation (maxlen, non-null), construct XHR object for db entry .
function sendData(dataSource, divID, englishtext, spanishtext) {

    var dataIsValid = true;
	var errorMsg = "Error: ";

    // checks non-nullable elements have data
	if (englishtext === "" || spanishtext === "") {
		errorMsg += "Critical (Non-nullable) value left empty!";
		dataIsValid = false;
	}

    // prints a error body of text to the identified div in index.html
	if (dataIsValid == false) {
        var place = document.getElementById(divID);
        place.innerHTML = errorMsg;
    } else { 
        // create the XHR object for sending to server. Must encode URI components to handle non A-z0-9 characters like ':'
        if(xhr) {
            var place = document.getElementById(divID);
            var requestBody = 
            "english=" + encodeURIComponent(englishtext) + 
			"&spanish=" + encodeURIComponent(spanishtext);

            xhr.open("POST", "/mdt/submittranslation.php", true);
			xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			// prints a response body of text to the identified div in index.html
			xhr.onreadystatechange = function() {
				if (xhr.readyState == 4 && xhr.status == 200) {
				place.innerHTML = xhr.responseText; 
				} // end if
			} // end anonymous call-back function
			xhr.send(requestBody);
		} // end if
    }
} // end function sendData()

// Sends english data only to database to check for similar translation items in db
function suggestData(dataSource, divID, englishtext) {
    var dataIsValid = true;
	var errorMsg = "Error: ";

    // checks non-nullable elements have data
	if (englishtext === "") {
		errorMsg += "Critical (Non-nullable) value left empty!";
		dataIsValid = false;
	}

    // prints a error body of text to the identified div in index.html
	if (dataIsValid == false) {
        var place = document.getElementById(divID);
        place.innerHTML = errorMsg;
    } else { 
        // create the XHR object for sending to server. Must encode URI components to handle non A-z0-9 characters like ':'
        if(xhr) {
            var place = document.getElementById(divID);
            var requestBody = 
            "english=" + encodeURIComponent(englishtext);

            xhr.open("POST", "/mdt/suggest.php", true);
			xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			// prints a response body of text to the identified div in index.html
			xhr.onreadystatechange = function() {
				if (xhr.readyState == 4 && xhr.status == 200) {
				place.innerHTML = xhr.responseText; 
				} // end if
			} // end anonymous call-back function
			xhr.send(requestBody);
		} // end if
    }
} // end function suggestData()
