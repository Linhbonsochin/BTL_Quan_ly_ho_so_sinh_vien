(function(){
    window.OCRHelper = {
        processFile: function(file, onResult, onError) {
            var form = new FormData();
            form.append('image', file);
            fetch('/BTL_17-09/handle/ocr_process.php', { method: 'POST', body: form, credentials: 'same-origin' })
                .then(function(resp){ return resp.json(); })
                .then(function(data){
                    if (!data.ok) {
                        (onError || console.error)(data.error || 'OCR failed');
                        return;
                    }
                    var text = data.text || '';
                    var parsed = OCRHelper.parseText(text);
                    (onResult || console.log)(parsed, text);
                }).catch(function(err){
                    (onError || console.error)(err);
                });
        },
        parseText: function(text) {
            var lines = text.split(/\r?\n/).map(function(l){ return l.trim(); }).filter(Boolean);
            var all = lines.join('\n');
            var parsed = {raw: text};
            var idMatch = all.match(/\b(\d{9,12})\b/);
            if (idMatch) parsed.cccd = idMatch[1];
            var dateMatch = all.match(/(\d{1,2}[\/\-]\d{1,2}[\/\-]\d{2,4})/);
            if (!dateMatch) dateMatch = all.match(/(\d{4}[\-]\d{1,2}[\-]\d{1,2})/);
            if (dateMatch) parsed.dob = dateMatch[1];
            var name = null;
            for (var i=0;i<lines.length;i++){
                var l = lines[i];
                if (l.length > 4 && /[A-Za-zÀ-ỹ\s]+/.test(l) && !/cccd|cmnd|số|ngày|năm|\d/.test(l.toLowerCase())) {
                    name = l; break;
                }
            }
            if (name) parsed.name = name;
            var addrLine = null;
            for (var i=0;i<lines.length;i++){
                var l = lines[i].toLowerCase();
                if (l.indexOf('phường')!==-1 || l.indexOf('xã')!==-1 || l.indexOf('quận')!==-1 || l.indexOf('thành phố')!==-1 || l.indexOf('thành ph')!==-1 || l.indexOf('tỉnh')!==-1) {
                    addrLine = lines[i]; break;
                }
            }
            if (addrLine) parsed.address = addrLine;
            return parsed;
        },
        autofillToForm: function(parsed) {
            if (!parsed) return 0;
            var count = 0;
            var map = [
                {k:'cccd', selectors:['input[name="cccd"]','input[name="so_cccd"]','input[name="cccd_number"]','input[name="soccid"]','input[name="cccd"]','input[id^="cccd"]']},
                {k:'name', selectors:['input[name="full_name"]','input[name="name"]','input[name="ho_ten"]','input[id^="name"]','input[name="hoten"]']},
                {k:'dob', selectors:['input[name="dob"]','input[name="ngaysinh"]','input[name="date_of_birth"]','input[id^="dob"]']},
                {k:'address', selectors:['input[name="address"]','input[name="dia_chi"]','textarea[name="address"]']}
            ];
            map.forEach(function(m){
                if (!parsed[m.k]) return;
                for (var i=0;i<m.selectors.length;i++){
                    var el = document.querySelector(m.selectors[i]);
                    if (el) { el.value = parsed[m.k]; count++; break; }
                }
            });
            return count;
        }
    };
})();
