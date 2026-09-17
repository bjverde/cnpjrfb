/**
 * Returns the URL Base
 */
function __adianti_base_url()
{
   return window.location.protocol +'//'+ window.location.host + window.location.pathname.split( '/' ).slice(0,-1).join('/');
}

/**
 * Register URL state
 */
function __adianti_register_state(url, origin)
{
    if (Adianti.registerState !== false || origin == 'user')
    {
        var stateObj = { url: url };
        if (typeof history.pushState != 'undefined') {
            history.pushState(stateObj, "", url.replace('engine.php', 'index.php').replace('xhr-', '').replace('&page_fragment=', '&_pf=').replace('&target_container=', '&_tc='));
        }
    }
}

/**
 * Returns the query string
 */
function __adianti_query_string(query_source)
{
    var query_string = {};
    var query = query_source || window.location.search.substring(1);
    var vars = query.split("&");
    for (var i=0; i<vars.length; i++)
    {
        var pair = vars[i].split("=");
        if (typeof query_string[pair[0]] === "undefined")
        {
            query_string[pair[0]] = pair[1];
            // If second entry with this name
        }
        else if (typeof query_string[pair[0]] === "string")
        {
            var arr = [ query_string[pair[0]], pair[1] ];
            query_string[pair[0]] = arr;
        }
        else
        {
            query_string[pair[0]].push(pair[1]);
        }
    }
    return query_string;
}

/**
 * Converts query string into json object
 */
function __adianti_query_to_json(query)
{
    var pieces = query.split('&');
    var params = Object();
    var decode = function (s) {
        if (typeof s !== "undefined"){
            return urldecode(s.replace(/\+/g, " "));
        }
        return s;
    };

    for (var i=0; i < pieces.length ; i++) {
        var part = pieces[i].split('=');
        if(part[0].search("\\[\\]") !== -1) {
            part[0]=part[0].replace(/\[\]$/,'');
            if( typeof params[part[0]] === 'undefined' ) {
                params[part[0]] = [decode(part[1])];

            } else {
                params[part[0]].push(decode(part[1]));
            }


        } else {
            params[part[0]] = decode(part[1]);
        }
    }

    return params;
}

function __adianti_base64_to_blob(b64Data, contentType='', sliceSize=512)
{
  const byteCharacters = atob(b64Data);
  const byteArrays = [];

  for (let offset = 0; offset < byteCharacters.length; offset += sliceSize) {
    const slice = byteCharacters.slice(offset, offset + sliceSize);

    const byteNumbers = new Array(slice.length);
    for (let i = 0; i < slice.length; i++) {
      byteNumbers[i] = slice.charCodeAt(i);
    }

    const byteArray = new Uint8Array(byteNumbers);
    byteArrays.push(byteArray);
  }

  const blob = new Blob(byteArrays, {type: contentType});
  return blob;
}

function __adianti_download_blob(blob, name = 'file.txt') {
  // Convert your blob into a Blob URL (a special url that points to an object in the browser's memory)
  const blobUrl = URL.createObjectURL(blob);

  // Create a link element
  const link = document.createElement("a");

  // Set link's href to point to the Blob URL
  link.href = blobUrl;
  link.download = name;

  // Append link to the body
  document.body.appendChild(link);

  // Dispatch click event on the link
  // This is necessary as link.click() does not work on the latest firefox
  link.dispatchEvent(
    new MouseEvent('click', { 
      bubbles: true, 
      cancelable: true, 
      view: window 
    })
  );

  // Remove link from body
  document.body.removeChild(link);
}
