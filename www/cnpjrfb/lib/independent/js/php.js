function base64_encode (stringToEncode) { // eslint-disable-line camelcase
  //  discuss at: https://locutus.io/php/base64_encode/
  // original by: Tyler Akins (https://rumkin.com)
  // improved by: Bayron Guevara
  // improved by: Thunder.m
  // improved by: Kevin van Zonneveld (https://kvz.io)
  // improved by: Kevin van Zonneveld (https://kvz.io)
  // improved by: Rafał Kukawski (https://blog.kukawski.pl)
  // bugfixed by: Pellentesque Malesuada
  // improved by: Indigo744
  //   example 1: base64_encode('Kevin van Zonneveld')
  //   returns 1: 'S2V2aW4gdmFuIFpvbm5ldmVsZA=='
  //   example 2: base64_encode('a')
  //   returns 2: 'YQ=='
  //   example 3: base64_encode('✓ à la mode')
  //   returns 3: '4pyTIMOgIGxhIG1vZGU='

  // encodeUTF8string()
  // Internal function to encode properly UTF8 string
  // Adapted from Solution #1 at https://developer.mozilla.org/en-US/docs/Web/API/WindowBase64/Base64_encoding_and_decoding
  var encodeUTF8string = function (str) {
    // first we use encodeURIComponent to get percent-encoded UTF-8,
    // then we convert the percent encodings into raw bytes which
    // can be fed into the base64 encoding algorithm.
    return encodeURIComponent(str).replace(/%([0-9A-F]{2})/g,
      function toSolidBytes (match, p1) {
        return String.fromCharCode('0x' + p1)
      })
  }

  if (typeof window !== 'undefined') {
    if (typeof window.btoa !== 'undefined') {
      return window.btoa(encodeUTF8string(stringToEncode))
    }
  } else {
    return new Buffer(stringToEncode).toString('base64')
  }

  var b64 = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/='
  var o1
  var o2
  var o3
  var h1
  var h2
  var h3
  var h4
  var bits
  var i = 0
  var ac = 0
  var enc = ''
  var tmpArr = []

  if (!stringToEncode) {
    return stringToEncode
  }

  stringToEncode = encodeUTF8string(stringToEncode)

  do {
    // pack three octets into four hexets
    o1 = stringToEncode.charCodeAt(i++)
    o2 = stringToEncode.charCodeAt(i++)
    o3 = stringToEncode.charCodeAt(i++)

    bits = o1 << 16 | o2 << 8 | o3

    h1 = bits >> 18 & 0x3f
    h2 = bits >> 12 & 0x3f
    h3 = bits >> 6 & 0x3f
    h4 = bits & 0x3f

    // use hexets to index into b64, and append result to encoded string
    tmpArr[ac++] = b64.charAt(h1) + b64.charAt(h2) + b64.charAt(h3) + b64.charAt(h4)
  } while (i < stringToEncode.length)

  enc = tmpArr.join('')

  var r = stringToEncode.length % 3

  return (r ? enc.slice(0, r - 3) : enc) + '==='.slice(r || 3)
}

function base64_decode (encodedData) { // eslint-disable-line camelcase
  //  discuss at: https://locutus.io/php/base64_decode/
  // original by: Tyler Akins (https://rumkin.com)
  // improved by: Thunder.m
  // improved by: Kevin van Zonneveld (https://kvz.io)
  // improved by: Kevin van Zonneveld (https://kvz.io)
  //    input by: Aman Gupta
  //    input by: Brett Zamir (https://brett-zamir.me)
  // bugfixed by: Onno Marsman (https://twitter.com/onnomarsman)
  // bugfixed by: Pellentesque Malesuada
  // bugfixed by: Kevin van Zonneveld (https://kvz.io)
  // improved by: Indigo744
  //   example 1: base64_decode('S2V2aW4gdmFuIFpvbm5ldmVsZA==')
  //   returns 1: 'Kevin van Zonneveld'
  //   example 2: base64_decode('YQ==')
  //   returns 2: 'a'
  //   example 3: base64_decode('4pyTIMOgIGxhIG1vZGU=')
  //   returns 3: '✓ à la mode'

  // decodeUTF8string()
  // Internal function to decode properly UTF8 string
  // Adapted from Solution #1 at https://developer.mozilla.org/en-US/docs/Web/API/WindowBase64/Base64_encoding_and_decoding
  var decodeUTF8string = function (str) {
    // Going backwards: from bytestream, to percent-encoding, to original string.
    return decodeURIComponent(str.split('').map(function (c) {
      return '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2)
    }).join(''))
  }

  if (typeof window !== 'undefined') {
    if (typeof window.atob !== 'undefined') {
      return decodeUTF8string(window.atob(encodedData))
    }
  } else {
    return new Buffer(encodedData, 'base64').toString('utf-8')
  }

  var b64 = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/='
  var o1
  var o2
  var o3
  var h1
  var h2
  var h3
  var h4
  var bits
  var i = 0
  var ac = 0
  var dec = ''
  var tmpArr = []

  if (!encodedData) {
    return encodedData
  }

  encodedData += ''

  do {
    // unpack four hexets into three octets using index points in b64
    h1 = b64.indexOf(encodedData.charAt(i++))
    h2 = b64.indexOf(encodedData.charAt(i++))
    h3 = b64.indexOf(encodedData.charAt(i++))
    h4 = b64.indexOf(encodedData.charAt(i++))

    bits = h1 << 18 | h2 << 12 | h3 << 6 | h4

    o1 = bits >> 16 & 0xff
    o2 = bits >> 8 & 0xff
    o3 = bits & 0xff

    if (h3 === 64) {
      tmpArr[ac++] = String.fromCharCode(o1)
    } else if (h4 === 64) {
      tmpArr[ac++] = String.fromCharCode(o1, o2)
    } else {
      tmpArr[ac++] = String.fromCharCode(o1, o2, o3)
    }
  } while (i < encodedData.length)

  dec = tmpArr.join('')

  return decodeUTF8string(dec.replace(/\0+$/, ''))
}

function number_format (number, decimals, decPoint, thousandsSep) { // eslint-disable-line camelcase
  //  discuss at: https://locutus.io/php/number_format/
  // original by: Jonas Raoni Soares Silva (https://www.jsfromhell.com)
  // improved by: Kevin van Zonneveld (https://kvz.io)
  // improved by: davook
  // improved by: Brett Zamir (https://brett-zamir.me)
  // improved by: Brett Zamir (https://brett-zamir.me)
  // improved by: Theriault (https://github.com/Theriault)
  // improved by: Kevin van Zonneveld (https://kvz.io)
  // bugfixed by: Michael White (https://getsprink.com)
  // bugfixed by: Benjamin Lupton
  // bugfixed by: Allan Jensen (https://www.winternet.no)
  // bugfixed by: Howard Yeend
  // bugfixed by: Diogo Resende
  // bugfixed by: Rival
  // bugfixed by: Brett Zamir (https://brett-zamir.me)
  //  revised by: Jonas Raoni Soares Silva (https://www.jsfromhell.com)
  //  revised by: Luke Smith (https://lucassmith.name)
  //    input by: Kheang Hok Chin (https://www.distantia.ca/)
  //    input by: Jay Klehr
  //    input by: Amir Habibi (https://www.residence-mixte.com/)
  //    input by: Amirouche
  //   example 1: number_format(1234.56)
  //   returns 1: '1,235'
  //   example 2: number_format(1234.56, 2, ',', ' ')
  //   returns 2: '1 234,56'
  //   example 3: number_format(1234.5678, 2, '.', '')
  //   returns 3: '1234.57'
  //   example 4: number_format(67, 2, ',', '.')
  //   returns 4: '67,00'
  //   example 5: number_format(1000)
  //   returns 5: '1,000'
  //   example 6: number_format(67.311, 2)
  //   returns 6: '67.31'
  //   example 7: number_format(1000.55, 1)
  //   returns 7: '1,000.6'
  //   example 8: number_format(67000, 5, ',', '.')
  //   returns 8: '67.000,00000'
  //   example 9: number_format(0.9, 0)
  //   returns 9: '1'
  //  example 10: number_format('1.20', 2)
  //  returns 10: '1.20'
  //  example 11: number_format('1.20', 4)
  //  returns 11: '1.2000'
  //  example 12: number_format('1.2000', 3)
  //  returns 12: '1.200'
  //  example 13: number_format('1 000,50', 2, '.', ' ')
  //  returns 13: '100 050.00'
  //  example 14: number_format(1e-8, 8, '.', '')
  //  returns 14: '0.00000001'

  number = (number + '').replace(/[^0-9+\-Ee.]/g, '')
  var n = !isFinite(+number) ? 0 : +number
  var prec = !isFinite(+decimals) ? 0 : Math.abs(decimals)
  var sep = (typeof thousandsSep === 'undefined') ? ',' : thousandsSep
  var dec = (typeof decPoint === 'undefined') ? '.' : decPoint
  var s = ''

  var toFixedFix = function (n, prec) {
    if (('' + n).indexOf('e') === -1) {
      return +(Math.round(n + 'e+' + prec) + 'e-' + prec)
    } else {
      var arr = ('' + n).split('e')
      var sig = ''
      if (+arr[1] + prec > 0) {
        sig = '+'
      }
      return (+(Math.round(+arr[0] + 'e' + sig + (+arr[1] + prec)) + 'e-' + prec)).toFixed(prec)
    }
  }

  // @todo: for IE parseFloat(0.55).toFixed(0) = 0;
  s = (prec ? toFixedFix(n, prec).toString() : '' + Math.round(n)).split('.')
  if (s[0].length > 3) {
    s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep)
  }
  if ((s[1] || '').length < prec) {
    s[1] = s[1] || ''
    s[1] += new Array(prec - s[1].length + 1).join('0')
  }

  return s.join(dec)
}

function urlencode (str) {
  //       discuss at: https://locutus.io/php/urlencode/
  //      original by: Philip Peterson
  //      improved by: Kevin van Zonneveld (https://kvz.io)
  //      improved by: Kevin van Zonneveld (https://kvz.io)
  //      improved by: Brett Zamir (https://brett-zamir.me)
  //      improved by: Lars Fischer
  //      improved by: Waldo Malqui Silva (https://fayr.us/waldo/)
  //         input by: AJ
  //         input by: travc
  //         input by: Brett Zamir (https://brett-zamir.me)
  //         input by: Ratheous
  //      bugfixed by: Kevin van Zonneveld (https://kvz.io)
  //      bugfixed by: Kevin van Zonneveld (https://kvz.io)
  //      bugfixed by: Joris
  // reimplemented by: Brett Zamir (https://brett-zamir.me)
  // reimplemented by: Brett Zamir (https://brett-zamir.me)
  //           note 1: This reflects PHP 5.3/6.0+ behavior
  //           note 1: Please be aware that this function
  //           note 1: expects to encode into UTF-8 encoded strings, as found on
  //           note 1: pages served as UTF-8
  //        example 1: urlencode('Kevin van Zonneveld!')
  //        returns 1: 'Kevin+van+Zonneveld%21'
  //        example 2: urlencode('https://kvz.io/')
  //        returns 2: 'https%3A%2F%2Fkvz.io%2F'
  //        example 3: urlencode('https://www.google.nl/search?q=Locutus&ie=utf-8')
  //        returns 3: 'https%3A%2F%2Fwww.google.nl%2Fsearch%3Fq%3DLocutus%26ie%3Dutf-8'

  str = (str + '')

  return encodeURIComponent(str)
    .replace(/!/g, '%21')
    .replace(/'/g, '%27')
    .replace(/\(/g, '%28')
    .replace(/\)/g, '%29')
    .replace(/\*/g, '%2A')
    .replace(/~/g, '%7E')
    .replace(/%20/g, '+')
}

function urldecode (str) {
  //       discuss at: https://locutus.io/php/urldecode/
  //      original by: Philip Peterson
  //      improved by: Kevin van Zonneveld (https://kvz.io)
  //      improved by: Kevin van Zonneveld (https://kvz.io)
  //      improved by: Brett Zamir (https://brett-zamir.me)
  //      improved by: Lars Fischer
  //      improved by: Orlando
  //      improved by: Brett Zamir (https://brett-zamir.me)
  //      improved by: Brett Zamir (https://brett-zamir.me)
  //         input by: AJ
  //         input by: travc
  //         input by: Brett Zamir (https://brett-zamir.me)
  //         input by: Ratheous
  //         input by: e-mike
  //         input by: lovio
  //      bugfixed by: Kevin van Zonneveld (https://kvz.io)
  //      bugfixed by: Rob
  // reimplemented by: Brett Zamir (https://brett-zamir.me)
  //           note 1: info on what encoding functions to use from:
  //           note 1: https://xkr.us/articles/javascript/encode-compare/
  //           note 1: Please be aware that this function expects to decode
  //           note 1: from UTF-8 encoded strings, as found on
  //           note 1: pages served as UTF-8
  //        example 1: urldecode('Kevin+van+Zonneveld%21')
  //        returns 1: 'Kevin van Zonneveld!'
  //        example 2: urldecode('https%3A%2F%2Fkvz.io%2F')
  //        returns 2: 'https://kvz.io/'
  //        example 3: urldecode('https%3A%2F%2Fwww.google.nl%2Fsearch%3Fq%3DLocutus%26ie%3Dutf-8%26oe%3Dutf-8%26aq%3Dt%26rls%3Dcom.ubuntu%3Aen-US%3Aunofficial%26client%3Dfirefox-a')
  //        returns 3: 'https://www.google.nl/search?q=Locutus&ie=utf-8&oe=utf-8&aq=t&rls=com.ubuntu:en-US:unofficial&client=firefox-a'
  //        example 4: urldecode('%E5%A5%BD%3_4')
  //        returns 4: '\u597d%3_4'

  return decodeURIComponent((str + '')
    .replace(/%(?![\da-f]{2})/gi, function () {
      // PHP tolerates poorly formed escape sequences
      return '%25'
    })
    .replace(/\+/g, '%20'))
}

function serialize (mixedValue) {
  //  discuss at: https://locutus.io/php/serialize/
  // original by: Arpad Ray (mailto:arpad@php.net)
  // improved by: Dino
  // improved by: Le Torbi (https://www.letorbi.de/)
  // improved by: Kevin van Zonneveld (https://kvz.io/)
  // bugfixed by: Andrej Pavlovic
  // bugfixed by: Garagoth
  // bugfixed by: Russell Walker (https://www.nbill.co.uk/)
  // bugfixed by: Jamie Beck (https://www.terabit.ca/)
  // bugfixed by: Kevin van Zonneveld (https://kvz.io/)
  // bugfixed by: Ben (https://benblume.co.uk/)
  // bugfixed by: Codestar (https://codestarlive.com/)
  // bugfixed by: idjem (https://github.com/idjem)
  //    input by: DtTvB (https://dt.in.th/2008-09-16.string-length-in-bytes.html)
  //    input by: Martin (https://www.erlenwiese.de/)
  //      note 1: We feel the main purpose of this function should be to ease
  //      note 1: the transport of data between php & js
  //      note 1: Aiming for PHP-compatibility, we have to translate objects to arrays
  //   example 1: serialize(['Kevin', 'van', 'Zonneveld'])
  //   returns 1: 'a:3:{i:0;s:5:"Kevin";i:1;s:3:"van";i:2;s:9:"Zonneveld";}'
  //   example 2: serialize({firstName: 'Kevin', midName: 'van'})
  //   returns 2: 'a:2:{s:9:"firstName";s:5:"Kevin";s:7:"midName";s:3:"van";}'
  //   example 3: serialize( {'ü': 'ü', '四': '四', '𠜎': '𠜎'})
  //   returns 3: 'a:3:{s:2:"ü";s:2:"ü";s:3:"四";s:3:"四";s:4:"𠜎";s:4:"𠜎";}'
  let val, key, okey
  let ktype = ''
  let vals = ''
  let count = 0
  const _utf8Size = function (str) {
    return ~-encodeURI(str).split(/%..|./).length
  }
  const _getType = function (inp) {
    let match
    let key
    let cons
    let types
    let type = typeof inp
    if (type === 'object' && !inp) {
      return 'null'
    }
    if (type === 'object') {
      if (!inp.constructor) {
        return 'object'
      }
      cons = inp.constructor.toString()
      match = cons.match(/(\w+)\(/)
      if (match) {
        cons = match[1].toLowerCase()
      }
      types = ['boolean', 'number', 'string', 'array']
      for (key in types) {
        if (cons === types[key]) {
          type = types[key]
          break
        }
      }
    }
    return type
  }
  const type = _getType(mixedValue)
  switch (type) {
    case 'function':
      val = ''
      break
    case 'boolean':
      val = 'b:' + (mixedValue ? '1' : '0')
      break
    case 'number':
      val = (Math.round(mixedValue) === mixedValue ? 'i' : 'd') + ':' + mixedValue
      break
    case 'string':
      val = 's:' + _utf8Size(mixedValue) + ':"' + mixedValue + '"'
      break
    case 'array':
    case 'object':
      val = 'a'
      /*
      if (type === 'object') {
        var objname = mixedValue.constructor.toString().match(/(\w+)\(\)/);
        if (objname === undefined) {
          return;
        }
        objname[1] = serialize(objname[1]);
        val = 'O' + objname[1].substring(1, objname[1].length - 1);
      }
      */
      for (key in mixedValue) {
        if (mixedValue.hasOwnProperty(key)) {
          ktype = _getType(mixedValue[key])
          if (ktype === 'function') {
            continue
          }
          okey = (key.match(/^[0-9]+$/) ? parseInt(key, 10) : key)
          vals += serialize(okey) + serialize(mixedValue[key])
          count++
        }
      }
      val += ':' + count + ':{' + vals + '}'
      break
    case 'undefined':
    default:
      // Fall-through
      // if the JS object has a property which contains a null value,
      // the string cannot be unserialized by PHP
      val = 'N'
      break
  }
  if (type !== 'object' && type !== 'array') {
    val += ';'
  }
  return val
}

function unserialize (data) {
  //  discuss at: https://locutus.io/php/unserialize/
  // original by: Arpad Ray (mailto:arpad@php.net)
  // improved by: Pedro Tainha (https://www.pedrotainha.com)
  // improved by: Kevin van Zonneveld (https://kvz.io)
  // improved by: Kevin van Zonneveld (https://kvz.io)
  // improved by: Chris
  // improved by: James
  // improved by: Le Torbi
  // improved by: Eli Skeggs
  // bugfixed by: dptr1988
  // bugfixed by: Kevin van Zonneveld (https://kvz.io)
  // bugfixed by: Brett Zamir (https://brett-zamir.me)
  // bugfixed by: philippsimon (https://github.com/philippsimon/)
  //  revised by: d3x
  //    input by: Brett Zamir (https://brett-zamir.me)
  //    input by: Martin (https://www.erlenwiese.de/)
  //    input by: kilops
  //    input by: Jaroslaw Czarniak
  //    input by: lovasoa (https://github.com/lovasoa/)
  // improved by: Rafał Kukawski
  //      note 1: We feel the main purpose of this function should be
  //      note 1: to ease the transport of data between php & js
  //      note 1: Aiming for PHP-compatibility, we have to translate objects to arrays
  //   example 1: unserialize('a:3:{i:0;s:5:"Kevin";i:1;s:3:"van";i:2;s:9:"Zonneveld";}')
  //   returns 1: ['Kevin', 'van', 'Zonneveld']
  //   example 2: unserialize('a:2:{s:9:"firstName";s:5:"Kevin";s:7:"midName";s:3:"van";}')
  //   returns 2: {firstName: 'Kevin', midName: 'van'}
  //   example 3: unserialize('a:3:{s:2:"ü";s:2:"ü";s:3:"四";s:3:"四";s:4:"𠜎";s:4:"𠜎";}')
  //   returns 3: {'ü': 'ü', '四': '四', '𠜎': '𠜎'}
  //   example 4: unserialize(undefined)
  //   returns 4: false
  //   example 5: unserialize('O:8:"stdClass":1:{s:3:"foo";b:1;}')
  //   returns 5: { foo: true }

  var utf8Overhead = function (str) {
    var s = str.length
    for (var i = str.length - 1; i >= 0; i--) {
      var code = str.charCodeAt(i)
      if (code > 0x7f && code <= 0x7ff) {
        s++
      } else if (code > 0x7ff && code <= 0xffff) {
        s += 2
      }
      // trail surrogate
      if (code >= 0xDC00 && code <= 0xDFFF) {
        i--
      }
    }
    return s - 1
  }
  var readUntil = function (data, offset, stopchr) {
    var i = 2
    var buf = []
    var chr = data.slice(offset, offset + 1)

    while (chr !== stopchr) {
      if ((i + offset) > data.length) {
        throw Error('Invalid')
      }
      buf.push(chr)
      chr = data.slice(offset + (i - 1), offset + i)
      i += 1
    }
    return [buf.length, buf.join('')]
  }
  var readChrs = function (data, offset, length) {
    var i, chr, buf

    buf = []
    for (i = 0; i < length; i++) {
      chr = data.slice(offset + (i - 1), offset + i)
      buf.push(chr)
      length -= utf8Overhead(chr)
    }
    return [buf.length, buf.join('')]
  }
  function _unserialize (data, offset) {
    var dtype
    var dataoffset
    var keyandchrs
    var keys
    var contig
    var length
    var array
    var obj
    var readdata
    var readData
    var ccount
    var stringlength
    var i
    var key
    var kprops
    var kchrs
    var vprops
    var vchrs
    var value
    var chrs = 0
    var typeconvert = function (x) {
      return x
    }

    if (!offset) {
      offset = 0
    }
    dtype = (data.slice(offset, offset + 1))

    dataoffset = offset + 2

    switch (dtype) {
      case 'i':
        typeconvert = function (x) {
          return parseInt(x, 10)
        }
        readData = readUntil(data, dataoffset, ';')
        chrs = readData[0]
        readdata = readData[1]
        dataoffset += chrs + 1
        break
      case 'b':
        typeconvert = function (x) {
          const value = parseInt(x, 10)

          switch (value) {
            case 0:
              return false
            case 1:
              return true
            default:
              throw SyntaxError('Invalid boolean value')
          }
        }
        readData = readUntil(data, dataoffset, ';')
        chrs = readData[0]
        readdata = readData[1]
        dataoffset += chrs + 1
        break
      case 'd':
        typeconvert = function (x) {
          return parseFloat(x)
        }
        readData = readUntil(data, dataoffset, ';')
        chrs = readData[0]
        readdata = readData[1]
        dataoffset += chrs + 1
        break
      case 'n':
        readdata = null
        break
      case 's':
        ccount = readUntil(data, dataoffset, ':')
        chrs = ccount[0]
        stringlength = ccount[1]
        dataoffset += chrs + 2

        readData = readChrs(data, dataoffset + 1, parseInt(stringlength, 10))
        chrs = readData[0]
        readdata = readData[1]
        dataoffset += chrs + 2
        if (chrs !== parseInt(stringlength, 10) && chrs !== readdata.length) {
          throw SyntaxError('String length mismatch')
        }
        break
      case 'a':
        readdata = {}

        keyandchrs = readUntil(data, dataoffset, ':')
        chrs = keyandchrs[0]
        keys = keyandchrs[1]
        dataoffset += chrs + 2

        length = parseInt(keys, 10)
        contig = true

        for (i = 0; i < length; i++) {
          kprops = _unserialize(data, dataoffset)
          kchrs = kprops[1]
          key = kprops[2]
          dataoffset += kchrs

          vprops = _unserialize(data, dataoffset)
          vchrs = vprops[1]
          value = vprops[2]
          dataoffset += vchrs

          if (key !== i) {
            contig = false
          }

          readdata[key] = value
        }

        if (contig) {
          array = new Array(length)
          for (i = 0; i < length; i++) {
            array[i] = readdata[i]
          }
          readdata = array
        }

        dataoffset += 1
        break
      case 'O': {
        // O:<class name length>:"class name":<prop count>:{<props and values>}
        // O:8:"stdClass":2:{s:3:"foo";s:3:"bar";s:3:"bar";s:3:"baz";}
        readData = readUntil(data, dataoffset, ':') // read class name length
        dataoffset += readData[0] + 1
        readData = readUntil(data, dataoffset, ':')

        if (readData[1] !== '"stdClass"') {
          throw Error('Unsupported object type: ' + readData[1])
        }

        dataoffset += readData[0] + 1 // skip ":"
        readData = readUntil(data, dataoffset, ':')
        keys = parseInt(readData[1], 10)

        dataoffset += readData[0] + 2 // skip ":{"
        obj = {}

        for (i = 0; i < keys; i++) {
          readData = _unserialize(data, dataoffset)
          key = readData[2]
          dataoffset += readData[1]

          readData = _unserialize(data, dataoffset)
          dataoffset += readData[1]
          obj[key] = readData[2]
        }

        dataoffset += 1 // skip "}"
        readdata = obj
        break
      }
      default:
        throw SyntaxError('Unknown / Unhandled data type(s): ' + dtype)
    }
    return [dtype, dataoffset - offset, typeconvert(readdata)]
  }

  try {
    if (typeof data !== 'string') {
      return false
    }

    return _unserialize(data, 0)[2]
  } catch (err) {
    console.error(err)
    return false
  }
}

function str_pad (input, padLength, padString, padType) { // eslint-disable-line camelcase
  //  discuss at: https://locutus.io/php/str_pad/
  // original by: Kevin van Zonneveld (https://kvz.io)
  // improved by: Michael White (https://getsprink.com)
  //    input by: Marco van Oort
  // bugfixed by: Brett Zamir (https://brett-zamir.me)
  //   example 1: str_pad('Kevin van Zonneveld', 30, '-=', 'STR_PAD_LEFT')
  //   returns 1: '-=-=-=-=-=-Kevin van Zonneveld'
  //   example 2: str_pad('Kevin van Zonneveld', 30, '-', 'STR_PAD_BOTH')
  //   returns 2: '------Kevin van Zonneveld-----'

  var half = ''
  var padToGo

  var _strPadRepeater = function (s, len) {
    var collect = ''

    while (collect.length < len) {
      collect += s
    }
    collect = collect.substr(0, len)

    return collect
  }

  input += ''
  padString = padString !== undefined ? padString : ' '

  if (padType !== 'STR_PAD_LEFT' && padType !== 'STR_PAD_RIGHT' && padType !== 'STR_PAD_BOTH') {
    padType = 'STR_PAD_RIGHT'
  }
  if ((padToGo = padLength - input.length) > 0) {
    if (padType === 'STR_PAD_LEFT') {
      input = _strPadRepeater(padString, padToGo) + input
    } else if (padType === 'STR_PAD_RIGHT') {
      input = input + _strPadRepeater(padString, padToGo)
    } else if (padType === 'STR_PAD_BOTH') {
      half = _strPadRepeater(padString, Math.ceil(padToGo / 2))
      input = half + input + half
      input = input.substr(0, padLength)
    }
  }

  return input
}
