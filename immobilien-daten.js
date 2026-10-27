/**
 * STATISCHE IMMOBILIEN-DATEN
 * ===========================
 * 
 * Hier werden alle Immobilien als statische Daten gepflegt.
 * Neue Immobilien können einfach als neues Objekt hinzugefügt werden.
 * 
 * Stand: Februar 2026
 * 
 * ANLEITUNG FÜR NEUE IMMOBILIEN:
 * 1. Kopiere ein bestehendes Objekt
 * 2. Ändere die ID (muss eindeutig sein)
 * 3. Fülle alle Felder aus
 * 4. Speichere die Datei
 */

const IMMOBILIEN_DATEN = [
    {
        id: 'usingen-3z-2026',
        titel: '3 Zimmer Wohnung mit Einbauküche in Usingen',
        kurzbeschreibung: 'Hochwertige Neubau-Wohnung mit 100 m² in erstklassiger Ausstattung',
        beschreibung: `Diese hochwertige Neubau-Wohnung bietet Ihnen 100 m² Wohnfläche in erstklassiger Ausstattung.

Die Wohnung verfügt über:
• 3 geräumige Zimmer
• 1 modernes Badezimmer mit Dusche
• 1 praktisches Gäste-WC
• Hochwertige Einbauküche
• Großer Balkon (8 m²) mit Südausrichtung
• Eigener Garten zur alleinigen Nutzung
• 3 PKW-Stellplätze
• Separater Abstellraum

Die Lage in Usingen bietet eine ideale Anbindung an Frankfurt und gleichzeitig die Ruhe des Taunus.`,
        typ: 'Wohnung',
        angebotsart: 'Miete',
        preis: 1600,
        nebenkosten: 220,
        heizkosten: 80,
        warmmiete: 1900,
        kaution: 2000,
        flaeche: 100,
        zimmer: 3,
        baeder: 1,
        ort: 'Usingen',
        plz: '61250',
        adresse: 'Usingen',
        verfuegbar: '01.04.2026',
        ausstattung: [
            'Neubau',
            'Einbauküche',
            'Badezimmer mit Dusche',
            'Gäste-WC',
            'Balkon (8 m²)',
            'Garten',
            '3 Parkplätze',
            'Abstellraum',
            'Fußbodenheizung'
        ],
        bilder: [
            'https://res.cloudinary.com/dlpdbr0ey/image/upload/v1770594231/Wohnung_zu_vermieten_Usingen_ec5chx.png'
        ],
        hauptbild: 'https://res.cloudinary.com/dlpdbr0ey/image/upload/v1770594231/Wohnung_zu_vermieten_Usingen_ec5chx.png',
        video: 'https://player.cloudinary.com/embed/?cloud_name=dlpdbr0ey&public_id=WhatsApp_Video_2026-02-01_at_23.03.11_jsmxts',
        status: 'verfuegbar',
        featured: true,
        erstellt: '2026-02-01'
    },
    {
        id: 'karben-2z-moebliert-2026',
        titel: '2 Zimmer Wohnung Möbliert in Karben',
        kurzbeschreibung: 'Gemütliche möblierte Wohnung - ideal für Singles oder Paare',
        beschreibung: `Diese gemütliche möblierte Wohnung ist ideal für Singles oder Paare und sofort bezugsfertig.

Die Wohnung umfasst:
• 2 Zimmer (Wohnzimmer + Schlafzimmer)
• Voll ausgestattete Einbauküche
• Modernes Badezimmer mit Dusche
• Praktischer Abstellraum
• Geräumiger Flur

Die Wohnung ist komplett möbliert und kann sofort bezogen werden. Die zentrale Lage in Karben ermöglicht eine schnelle Anbindung an Frankfurt und Bad Vilbel.`,
        typ: 'Wohnung',
        angebotsart: 'Miete',
        preis: 660,
        nebenkosten: 140,
        heizkosten: 60,
        warmmiete: 860,
        kaution: 2000,
        flaeche: 31,
        zimmer: 2,
        baeder: 1,
        ort: 'Karben',
        plz: '61184',
        adresse: 'Karben',
        verfuegbar: 'Sofort',
        ausstattung: [
            'Möbliert',
            'Einbauküche',
            'Badezimmer mit Dusche',
            'Abstellraum',
            'Sofort verfügbar',
            'Zentrale Lage'
        ],
        bilder: [
            'https://res.cloudinary.com/dlpdbr0ey/image/upload/v1770002887/WhatsApp_Image_2026-02-01_at_23.05.51_4_nr6xyb.jpg',
            'https://res.cloudinary.com/dlpdbr0ey/image/upload/v1770002887/WhatsApp_Image_2026-02-01_at_23.05.51_5_pnvf9y.jpg',
            'https://res.cloudinary.com/dlpdbr0ey/image/upload/v1770002888/WhatsApp_Image_2026-02-01_at_23.05.51_6_gysngj.jpg',
            'https://res.cloudinary.com/dlpdbr0ey/image/upload/v1770002889/WhatsApp_Image_2026-02-01_at_23.05.51_1_xobsew.jpg',
            'https://res.cloudinary.com/dlpdbr0ey/image/upload/v1770002889/WhatsApp_Image_2026-02-01_at_23.05.51_mvrcv5.jpg',
            'https://res.cloudinary.com/dlpdbr0ey/image/upload/v1770002889/WhatsApp_Image_2026-02-01_at_23.05.51_3_j5zmjb.jpg',
            'https://res.cloudinary.com/dlpdbr0ey/image/upload/v1770002889/WhatsApp_Image_2026-02-01_at_23.05.51_2_ehexg5.jpg'
        ],
        hauptbild: 'https://res.cloudinary.com/dlpdbr0ey/image/upload/v1770002887/WhatsApp_Image_2026-02-01_at_23.05.51_4_nr6xyb.jpg',
        video: null,
        status: 'verfuegbar',
        featured: true,
        erstellt: '2026-02-01'
    },
    {
        id: 'altenstadt-3z-2026',
        titel: '3 Zimmer Wohnung in Altenstadt - Nachmieter gesucht',
        kurzbeschreibung: 'Geräumige 3-Zimmer-Wohnung mit Balkon - Nachmieter gesucht',
        beschreibung: `3 Zimmer Wohnung in Altenstadt – Nachmieter gesucht

Diese geräumige Wohnung sucht einen Nachmieter.

Die Wohnung verfügt über:
• Küche
• Wohnzimmer
• Schlafzimmer
• Kinderzimmer
• Balkon
• Badezimmer mit Dusche
• Gäste-WC mit Wanne
• Abstellraum

Kontakt:
Ghumman Immobilien
Handy: 0160 98787878`,
        typ: 'Wohnung',
        angebotsart: 'Miete',
        preis: 1000,
        nebenkosten: 200,
        heizkosten: 50,
        warmmiete: 1250,
        kaution: 3000,
        flaeche: 76,
        zimmer: 3,
        baeder: 1,
        ort: 'Altenstadt',
        plz: '63674',
        adresse: 'Altenstadt',
        verfuegbar: 'Nach Vereinbarung',
        ausstattung: [
            'Küche',
            'Wohnzimmer',
            'Schlafzimmer',
            'Kinderzimmer',
            'Balkon',
            'Badezimmer mit Dusche',
            'Gäste-WC mit Wanne',
            'Abstellraum'
        ],
        bilder: [
            'https://res.cloudinary.com/dlpdbr0ey/image/upload/v1770657778/WhatsApp_Image_2026-02-09_at_04.30.12_ofglxy.jpg',
            'https://res.cloudinary.com/dlpdbr0ey/image/upload/v1770657779/WhatsApp_Image_2026-02-09_at_04.30.17_2_q3sofn.jpg',
            'https://res.cloudinary.com/dlpdbr0ey/image/upload/v1770657779/WhatsApp_Image_2026-02-09_at_04.30.14_1_bkndqn.jpg',
            'https://res.cloudinary.com/dlpdbr0ey/image/upload/v1770657777/WhatsApp_Image_2026-02-09_at_04.30.15_smheox.jpg',
            'https://res.cloudinary.com/dlpdbr0ey/image/upload/v1770657780/WhatsApp_Image_2026-02-09_at_04.30.14_4_qyc4d5.jpg',
            'https://res.cloudinary.com/dlpdbr0ey/image/upload/v1770657778/WhatsApp_Image_2026-02-09_at_04.30.16_c0cwsj.jpg',
            'https://res.cloudinary.com/dlpdbr0ey/image/upload/v1770662441/WhatsApp_Image_2026-02-09_at_04.30.15_3_uvgicz.jpg',
            'https://res.cloudinary.com/dlpdbr0ey/image/upload/v1770662447/WhatsApp_Image_2026-02-09_at_04.30.15_2_v9e4ob.jpg',
            'https://res.cloudinary.com/dlpdbr0ey/image/upload/v1770657777/WhatsApp_Image_2026-02-09_at_04.30.15_6_uzmzzz.jpg'
        ],
        hauptbild: 'https://res.cloudinary.com/dlpdbr0ey/image/upload/v1770657778/WhatsApp_Image_2026-02-09_at_04.30.12_ofglxy.jpg',
        video: null,
        status: 'verfuegbar',
        featured: true,
        erstellt: '2026-02-08'
    },
    {
        id: 'bad-vilbel-marktplatz-3z-2026',
        titel: '3 Zimmer Wohnung in Bad Vilbel Marktplatz - Nachmieter gesucht',
        kurzbeschreibung: 'Zentrale 3-Zimmer-Wohnung am Marktplatz - Verfügbar ab 01.04.2026',
        beschreibung: `3 Zimmer Wohnung in Bad Vilbel Marktplatz – Nachmieter gesucht ab 01.04.2026

Zentral gelegene Wohnung direkt am Marktplatz von Bad Vilbel.

Die Wohnung verfügt über:
• Wohnzimmer mit offener Küche
• Schlafzimmer
• Kinderzimmer
• Badezimmer mit Dusche
• Kellerraum

Kontakt:
Ghumman Immobilien
Handy: 0160 98787878`,
        typ: 'Wohnung',
        angebotsart: 'Miete',
        preis: 1000,
        nebenkosten: 200,
        heizkosten: 50,
        warmmiete: 1250,
        kaution: 3000,
        flaeche: 75,
        zimmer: 3,
        baeder: 1,
        ort: 'Bad Vilbel',
        plz: '61118',
        adresse: 'Marktplatz, Bad Vilbel',
        verfuegbar: '01.04.2026',
        ausstattung: [
            'Wohnzimmer mit offener Küche',
            'Schlafzimmer',
            'Kinderzimmer',
            'Badezimmer mit Dusche',
            'Kellerraum',
            'Zentrale Lage'
        ],
        bilder: [
            'https://res.cloudinary.com/dlpdbr0ey/image/upload/v1770593975/1_u6i8b8.png'
        ],
        hauptbild: 'https://res.cloudinary.com/dlpdbr0ey/image/upload/v1770593975/1_u6i8b8.png',
        video: null,
        status: 'verfuegbar',
        featured: true,
        erstellt: '2026-02-08'
    },
    {
        id: 'buedingen-3z-2026-03',
        titel: '3 Zimmer Wohnung in Buedingen',
        kurzbeschreibung: 'Ab sofort frei - neu renovierte Wohnung in Buedingen',
        beschreibung: `3 Zimmer Wohnung in Buedingen - Ab sofort frei

Neue Renovierung gemacht.

Die Wohnung verfuegt ueber:
• Kueche
• Wohnzimmer
• Schlafzimmer
• Kinderzimmer
• Flur
• Kellerraum
• Badezimmer mit Dusche

Kosten:
• Kaltmiete: 750 €
• Nebenkosten: 200 €
• Heizkosten: Selbst zu zahlen (Gaszaehler, Etagenheizung)
• Gesamtbetrag: 950 €
• Kaution: 2.250 €

Kontakt:
Ghumman Immobilien
Handy: 0160 98787878`,
        typ: 'Wohnung',
        angebotsart: 'Miete',
        preis: 750,
        nebenkosten: 200,
        heizkosten: 0,
        warmmiete: 950,
        kaution: 2250,
        flaeche: null,
        zimmer: 3,
        baeder: 1,
        ort: 'Buedingen',
        plz: '63654',
        adresse: 'Buedingen',
        verfuegbar: 'Sofort',
        ausstattung: [
            'Neu renoviert',
            'Kueche',
            'Wohnzimmer',
            'Schlafzimmer',
            'Kinderzimmer',
            'Flur',
            'Kellerraum',
            'Badezimmer mit Dusche',
            'Etagenheizung (Gaszaehler)'
        ],
        bilder: [
            'https://res.cloudinary.com/dlpdbr0ey/image/upload/v1773303246/Immobilien_Ghumman_Konto_z8ieci.png'
        ],
        hauptbild: 'https://res.cloudinary.com/dlpdbr0ey/image/upload/v1773303246/Immobilien_Ghumman_Konto_z8ieci.png',
        video: null,
        status: 'verfuegbar',
        featured: true,
        erstellt: '2026-03-12'
    },
    {
        id: 'altenstadt-4z-84qm-2026-03',
        titel: '4 Zimmer Wohnung in Altenstadt (84 qm)',
        kurzbeschreibung: '4 Zimmer Wohnung in Altenstadt mit Balkon und 84 qm Wohnflaeche',
        beschreibung: `4 Zimmer Wohnung in Altenstadt - Wohnflaeche 84 qm

Die Wohnung verfuegt ueber:
• Kueche
• Wohnzimmer
• 2 Schlafzimmer
• Kinderzimmer
• Abstellraum
• Badezimmer mit Wanne
• Gaeste-WC
• Balkon

Kosten:
• Kaltmiete: 1.100 €
• Nebenkosten: 180 €
• Heizkosten: 70 €
• Warmmiete: 1.350 €
• Kaution: 3.300 €

Kontakt:
Ghumman Immobilien
Handy: 0160 98787878`,
        typ: 'Wohnung',
        angebotsart: 'Miete',
        preis: 1100,
        nebenkosten: 180,
        heizkosten: 70,
        warmmiete: 1350,
        kaution: 3300,
        flaeche: 84,
        zimmer: 4,
        baeder: 1,
        ort: 'Altenstadt',
        plz: '63674',
        adresse: 'Altenstadt',
        verfuegbar: 'Nach Vereinbarung',
        ausstattung: [
            'Kueche',
            'Wohnzimmer',
            '2 Schlafzimmer',
            'Kinderzimmer',
            'Abstellraum',
            'Badezimmer mit Wanne',
            'Gaeste-WC',
            'Balkon'
        ],
        bilder: [
            'https://res.cloudinary.com/dlpdbr0ey/image/upload/v1773303246/Immobilien_Ghumman_Konto_1_rrlhgh.png'
        ],
        hauptbild: 'https://res.cloudinary.com/dlpdbr0ey/image/upload/v1773303246/Immobilien_Ghumman_Konto_1_rrlhgh.png',
        video: null,
        status: 'verfuegbar',
        featured: true,
        erstellt: '2026-03-12'
    }
];

/**
 * Hilfsfunktionen für den Zugriff auf Immobilien-Daten
 */

// Alle Immobilien abrufen
function getAlleImmobilien() {
    return IMMOBILIEN_DATEN;
}

// Immobilie nach ID suchen
function getImmobilieById(id) {
    return IMMOBILIEN_DATEN.find(immo => immo.id === id);
}

// Verfügbare Immobilien abrufen
function getVerfuegbareImmobilien() {
    return IMMOBILIEN_DATEN.filter(immo => immo.status === 'verfuegbar');
}

// Featured Immobilien abrufen
function getFeaturedImmobilien() {
    return IMMOBILIEN_DATEN.filter(immo => immo.featured === true);
}

// Immobilien nach Ort filtern
function getImmobilienNachOrt(ort) {
    return IMMOBILIEN_DATEN.filter(immo =>
        immo.ort.toLowerCase().includes(ort.toLowerCase())
    );
}

// Immobilien nach Typ filtern
function getImmobilienNachTyp(typ) {
    return IMMOBILIEN_DATEN.filter(immo =>
        immo.typ.toLowerCase() === typ.toLowerCase()
    );
}

// Preis formatieren
function formatPreis(preis) {
    return new Intl.NumberFormat('de-DE', {
        style: 'currency',
        currency: 'EUR',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    }).format(preis);
}

// Fläche formatieren
function formatFlaeche(flaeche) {
    return `${flaeche} m²`;
}
