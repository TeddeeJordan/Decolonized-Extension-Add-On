﻿var elements = document.getElementsByTagName('*');


// Declared here, defined at bottom of file
var tribeList = {
"Gros Ventre": "A'aninin",
"Mojave": "Aha Makhav",
"Mohave": "Aha Makhav",
"Alabama Tribe": "Albaamaha Tribe",
"Alabama Indian": "Albaamaha Indian",
"Alabama Nation": "Albaamaha Nation",
"Ojibwa": "Anishinaabe",
"Ojibwe": "Anishinaabe",
"Chippewa": "Anishinaabe",
"Crow People": "Apsáalooke People",
"Crow Tribe": "Apsáalooke Tribe",
"Nakota": "Assiniboine",
"Ioway": "Báxoǰe",
"Iowa People": "Báxoǰe people",
"Iowa Tribe": "Báxoǰe Tribe",
"Choctaw": "Chahta Yakni",
"Hoh people": "Chalá·at people",
"Hoh Tribe": "Chalá·at Tribe",
"Chetco": "Cheti",
"Chickasaw": "Chikasha",
"Dakota": "Dakhóta",
"Navajo Nation": "Naabeehó Bináhásdzo",
"Navajo": "Diné",
"Navajo": "Diné",
"Muscogee": "Este Mvskokvlke",
"Creek Nation": "Este Mvskokvlke Nation",
"Creek people": "Este Mvskokvlke people",
"Creek Tribe": "Este Mvskokvlke Tribe",
"Cayuga": "Gayogohó:no’",
"Oneida": "Haudenosaunee",
"Arapaho": "Hinono'eino",
"Arapahoe": "Hinono'eiteen",
"Hopi": "Hopituh Shi-nu-mu",
"Western Dakota": "Iháŋktȟuŋwaŋ",
"Eastern Dakota": "Isáŋyathi",
"Catawba": "Iswa",
"Shasta": "Kahosadi",
"Kainai": "Káínawa",
"Blood Tribe": "Káínawa",
"Plains Apache": "Kalth Tindé",
"Apache Tribe of Oklahoma": "Kalth Tindé Tribe of Oklahoma",
"Bay Mills Indian Community": "Gnoozhekaaning",
"Coushatta": "Koasati",
"Kutenai": "Ktunaxa",
"Kootenay": "Ktunaxa",
"Kootenai": "Ktunaxa",
"Cupeño": "Kuupangaxwichem",
"Cocopah": "Kwapa",
"Makah": "Kwih-dich-chuh-ahtx",
"Lakota": "Lakȟóta",
"Delaware People": "Lenape People",
"Delaware Tribe": "Lenape Tribe",
"Delaware Nation": "Lenape Nation",
"Cayuse": "Liksiyu",
"Menominee": "Mamaceqtaw",
"Miami": "Myaamiaki",
"Hupa": "Natinixwe",
"Hoopa": "Natinixwe",
"Apache": "N'de",
"Apaches": "N'de",
"Cree Nation": "Nēhiyaw Nation",
"Cree Tribe": "Nēhiyaw Tribe",
"Cree People": "Nēhiyaw People",
"Cree Indian": "Nēhiyaw Indian",
"Pottawatomi": "Neshnabé",
"Klallam": "nəxʷsƛ̕ay̕əm",
"Clallam": "nəxʷsƛ̕ay̕əm",
"Mattole": "Ni'ekeni",
"Bear River Indians": "Ni'ekeni",
"Odawa": "Nishnaabe",
"Ottawa": "Nishnaabe",
"Odaawaa": "Nishnaabe",
"Missouria": "Niúachi",
"Missouri people": "Niúachi people",
"Missouri Tribe": "Niúachi Tribe",
"Nooksack": "Noxwsʼáʔaq",
"Timbisha": "Nümü Tümpisattsi",
"Comanche": "Nʉmʉnʉʉ",
"Chemehuevi": "Nüwüwü",
"Mono Tribe": "Nyyhmy Tribe",
"Mono Nation": "Nyyhmy Nation",
"Mono people": "Nyyhmy people",
"Mono Indian": "Nyyhmy Indian",
"Sioux": "Očhéthi Šakówiŋ",
"Oglala Sioux": "Oglala",
"Yurok": "Olekwo'l",
"Onondaga": "Onöñda’gaga’",
"Luiseño": "Payómkawichum",
"Luiseno": "Payómkawichum",
"Piegan": "Piikáni",
"Piegan Blackfeet": "Piikáni",
"Maricopa": "Piipaash",
"Wenatchi": "P'squosa",
"Pend d’Oreilles": "Ql̓ispé",
"Kalispel": "Ql̓ispé",
"Klickitat": "Qwû'lh-hwai-pûm",
"Klikitat": "Qwû'lh-hwai-pûm",
"Coeur d'Alene": "Schitsu'umsh",
"Shawnee": "Shaawana",
"Blackfoot": "Siksikaitsitapi",
"Blackfeet": "Siksikaitsitapi",
"Siksika Nation": "Siksikáwa",
"Nisqually": "Squalli-Absch",
"Tolowa": "Taa-laa-wa Dee-ni",
"Serrano": "Taaqtam",
"the Crow": "the Apsáalooke",
"the Iowa": "the Báxoǰe",
"Hoh Tribe": "Chalá·at Tribe",
"Hoh people": "Chalá·at people",
"Hoh Nation": "Chalá·at Nation",
"Hoh Indian": "Chalá·at Indian",
"the Creek": "the Este Mvskokvlke",
"the Delaware": "the Lenape",
"Cherokee": "Tsalagi",
"Northern Cheyenne Indian Reservations": "Tsėhéstáno",
"Cheyenne": "Tsêhéstáno",
"Sarcee": "Tsuut'ina",
"Wasco": "Wacq!ó",
"Wasco-Wishram": "Wacq!ó-Wishram",
"Wanapum": "Wánapam",
"Osage": "Wazhazhe",
"Maliseet": "Wolastoqiyik",
"Tolowa": "Xvsh",
"Lummi": "Xwlemi",
"Cahuilla": "ʔívil̃uqaletem",
"Yaqui": "Yoeme",
"Nomlaki": "Nomlāqa",
"Pawnee": "Chaticks si Chaticks",
"Passamaquoddy": "Peskotomuhkati",
"Penobscot": "Panawahpskek",
"Peoria": "Peewaareewa",
"Meskwaki": "Meshkwahkihaki",
"Mesquakie": "Meshkwahkihaki",
"Skokomish": "sqʷuqʷóbəš",
"Chimakum": "Aqokúlo",
"Snoqualmie": "Sduk-al-bixw",
"Pabakse": "Iháŋkthuŋwaŋna",
"Sisseton": "Sisíthuŋwaŋ",
"Wahpeton": "Waȟpéthuŋwaŋ",
"Spokan ": "Spoqe'ind",
"Spokane": "Spoqe'ind",
"Stillaguamish": "Stoluck-wa-mish",
"St. Regis Mohawk Reservation": "Akwesasne",
"Mohawk Nation": "Kanien'kehá:ka",
"Mohawk People": "Kanien'kehá:ka people",
"Mohawk Tribe": "Kanien'kehá:ka Tribe",
"Mohawk Indian": "Kanien'kehá:ka Indian",
"Fox Tribe": "Meshkwahkihaki Tribe",
"Fox Nation": "Meshkwahkihaki Nation",
"Fox people": "Meshkwahkihaki people",
"Fox Indian": "Meshkwahkihaki Indian",
" Sac Tribe": " oθaakiiwaki Tribe",
" Sac People": " oθaakiiwaki people",
" Sac Nation": " oθaakiiwaki Nation",
"Sac and Fox": "oθaakiiwaki and Meshkwahkihaki",
"Sac & Fox": "oθaakiiwaki & Meshkwahkihaki",
"Sauk": "oθaakiiwaki",
"Poarch Band of Creek Indians": "Poarch Band of Mvskokvlke Indians",
"Poarch Band of Creeks": "Poarch Band of Mvskokvlke",
"Paskenta Band of Nomlaki Indians": "Nomlāqa Bōda",
"Mandan": "Numakaki",
"Hidatsa": "Hiraacá",
"Mandan": "Numakaki",
"Washoe": "Waashiw",
"Tonkawa": "Tickanwa•tic",
"Snohomish": "Sdoh-doh-hohbsh",
"Snohomish": "/sʔémǝš/",
"Wyandotte": "Wendat",
"Zuni": "A:shiwi",
"Biloxi": "Tanêks(a)",
"Tuscarora": "Skarù:ręˀ",
"Nez Perce": "Niimíipuu",
"Arikara": "Sahnish"



}

for (var i = 0; i < elements.length; i++) {
    var element = elements[i];

    for (var j = 0; j < element.childNodes.length; j++) {
        var node = element.childNodes[j];

        if (node.nodeType === Node.TEXT_NODE) {
            var text = node.nodeValue;
            var replacedText = text;

            // Create regex search string of tribe names (keys) separated by pipe
            // g = matches all instances
            // i = case-insensitive
            var regexStr = new RegExp(Object.keys(tribeList).join("|"),"gi");
            replacedText = replacedText.replace(regexStr, function(matched) {
                return tribeList[matched];
            });

            // If any matches were found, replace in document
            if (replacedText !== text) {
                element.replaceChild(document.createTextNode(replacedText), node);
            }
			
			
        }
    }
}
