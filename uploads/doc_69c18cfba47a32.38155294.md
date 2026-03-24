# 🏁 F1 API Dashboard

Progetto scolastico moderno e interattivo che fornisce una dashboard completa sulla Formula 1, integrando dati in tempo reale tramite **OpenF1 API**. Visualizza gare, classifiche, risultati e comunicazioni radio con un'interfaccia elegante e responsive.

---

## 📋 Descrizione
Questo progetto è stato sviluppato come lavoro scolastico per apprendere le tecnologie web moderne e l'integrazione con API REST. L'obiettivo principale è creare una piattaforma web che consenta agli utenti di esplorare dati della Formula 1 in maniera intuitiva e dinamica.


### 🎯 Obiettivi Didattici
- ✅ **Fetch API e Async/Await**: Gestione asincrona delle richieste HTTP
- ✅ **Manipolazione del DOM**: Creazione e modifica dinamica di elementi HTML
- ✅ **Elaborazione JSON**: Parsing e trasformazione di dati JSON
- ✅ **Gestione Errori**: Implementazione di strategie di error handling robusto
- ✅ **Responsive Design**: Creazione di interfacce adattabili a diversi dispositivi
- ✅ **Ciclo di vita delle applicazioni**: States, loading, error handling

---

## 🛠️ Tecnologie Utilizzate

- **HTML5** - Struttura semantica delle pagine
- **CSS3** - Styling avanzato con variabili CSS, Flexbox e Media Queries
- **JavaScript (ES6+)** - Logica applicativa con arrow functions, template literals, destructuring
- **OpenF1 API** - Fonte dati ufficiale per informazioni di Formula 1 ([https://openf1.org](https://openf1.org))
- **Fetch API** - Comunicazione client-server
- **Local Storage** - Persistenza dati di sessione (per login)

---

## ⚙️ Funzionalità Principali

### 🏠 Homepage
- Visualizzazione della prossima gara in programma
- Classifica piloti aggiornata in tempo reale
- Mini-giochi interattivi (Reaction Time Game, Mini Race Game)
- Sequenza animata dei semafori di partenza
- Effetti visivi con confetti e linee di velocità

### 📊 Storico Classifiche
- Selezione della stagione (da 2025 al 2026)
- Visualizzazione podio con primi tre classificati
- Classifica completa dei piloti e dei costruttori
- Toggle tra Piloti e Team
- Indicatore visivo del titolo campionato

### 🏁 Storico Gare
- Navigazione per anno/stagione
- Card delle gare con podio visivo
- Informazioni su data, circuito e risultati
- Load more dinamico per gestire molte gare
- Preview dei Top 3 per ogni gara

### 🔍 Dettagli Gara
- Informazioni complete del circuito
- Integrazione con Google Maps
- Meteo in tempo reale
- Griglia di partenza vs risultato finale
- Tabella completa con tutti i piloti partecipanti

### 👤 Profilo Pilota / Team
- Statistiche stagionali (punti, vittorie, podi)
- Storico risultati
- Informazioni team/scuderia
- Grafico prestazioni

### 📻 Team Radio
- Comunicazioni radio ufficiali
- Filtro per anno e gara
- Risposta asincrona con gestione caricamento

### 🔐 Login
- Area protetta per team radio
- Autenticazione locale (demo)
- Password toggle con sicurezza

---

## 📁 Struttura del Progetto

```
Sesana_Manzoni-ProgTPS_API/
├── 📁 css
│   ├── 🎨 storico_gare.css
│   └── 🎨 style.css
├── 📁 img
│   ├── 📁 sito
│   │   ├── 🖼️ classifiche.png
│   │   ├── 🖼️ ...
│   ├── 🖼️ logof1.png
│   └── 🖼️ ...
├── 📁 js
│   ├── 📄 apiManager.js
│   ├── 📄 dettaglio.js
│   ├── 📄 index.js
│   ├── 📄 info_gara.js
│   ├── 📄 login.js
│   ├── 📄 storico_classifica.js
│   ├── 📄 storico_gare.js
│   └── 📄 team_radio.js
├── ⚙️ .gitignore
├── 📝 README.md
├── 🌐 dettaglio.html
├── 🌐 index.html
├── 🌐 info_gara.html
├── 🌐 login.html
├── 🌐 storico_classifica.html
├── 🌐 storico_gare.html
└── 🌐 team_radio.html
```

## Navigazione tra le pagine

**`index.html`** (Home)
- → `storico_gare.html` (navbar + pulsante "Storico Gare")
- → `storico_classifica.html` (navbar + pulsante "Storico Classifiche")
- → `login.html` (navbar + pulsante "Effettua il Login")

**`storico_gare.html`**
- → `index.html` (logo navbar)
- → `storico_classifica.html` (navbar)
- → `login.html` (navbar)
- → `info_gara.html` (click su una card gara)

**`info_gara.html`**
- → `index.html` (logo navbar)
- → `storico_gare.html` (navbar)
- → `storico_classifica.html` (navbar)
- → `login.html` (navbar)
- → Google Maps (link esterno)

**`storico_classifica.html`**
- → `index.html` (logo navbar)
- → `storico_gare.html` (navbar)
- → `login.html` (navbar)
- → `dettaglio.html` (click su pilota/costruttore)

**`dettaglio.html`**
- → `index.html` (logo navbar)
- → `storico_gare.html` (navbar)
- → `storico_classifica.html` (navbar + pulsante "Torna alla classifica")
- → `login.html` (navbar)

**`login.html`**
- → `index.html` (pulsante "← Indietro")
- → `team_radio.html` (dopo login riuscito)

**`team_radio.html`**
- → `index.html` (logo navbar)
- → `storico_gare.html` (navbar)
- → `storico_classifica.html` (navbar)
- → `login.html` (navbar)


### 📝 Descrizione dei File Principali

| File                      | Descrizione                                            |
| ------------------------- | ------------------------------------------------------ |
| `index.html`              | Pagina di benvenuto con ultime gare e classifica       |
| `style.css`               | Tema globale: variabili CSS, navbar, cards, responsive |
| `storico_gare.html`       | Visualizzazione gare per stagione con podio            |
| `storico_classifica.html` | Classifiche piloti e team per anno                     |
| `info_gara.html`          | Dettagli completi di una gara specifica                |
| `dettaglio.html`          | Profilo del pilota o team con statistiche              |
| `login.js`                | Gestione sessione utente (localStorage)                |

---

## 🔗 Integrazione con le OpenF1 API

### 📡 Come Funziona

L'applicazione comunica con le **OpenF1 API** per ottenere dati in tempo reale sulla Formula 1.

```javascript
// Configurazione base
const API_BASE_URL = 'https://api.openf1.org/v1';

// Fetch generico con retry automatico
async function fetchRetry(url, maxRetries = 3) {
    for (let attempt = 1; attempt <= maxRetries; attempt++) {
        try {
            const response = await fetch(url);
            if (!response.ok) throw new Error(`HTTP ${response.status}`);
            return await response.json();
        } catch (error) {
            if (attempt === maxRetries) throw error;
            await new Promise(r => setTimeout(r, 1000 * attempt));
        }
    }
}
```

### � Sistema di Cache

L'applicazione implementa un **sistema di cache intelligente** basato su `localStorage` per ottimizzare le prestazioni e ridurre il carico sulle API.

#### 🔄 Come Funziona

```
┌─────────────────────────────────────────────────────────┐
│ Richiesta API                                           │
└────────────────┬────────────────────────────────────────┘
                 │
         ┌───────▼────────┐
         │ Cache valida?  │
         └───────┬────────┘
          YES   │   NO
              ┌─┴─┐
              │   │
          ┌───▼┐ ┌▼────────────────┐
          │    │ │ API disponibile?│
       ┌──▼─┐  │ └────────┬────────┘
       │USE │  │     YES  │   NO
       │📦  │  │         ┌┴────────┐
       └────┘  │         │         │
              ┌▼──────┐ ┌▼────────┐
              │UPDATE │ │FALLBACK │
              │🌐 API │ │📦 Cache │
              │BADGE  │ │Scaduta  │
              └┬──────┘ └─┬───────┘
               │          │
            ┌──┴────────┬──┴──┐
            │           │     │
         ┌──▼──────┐ ┌──▼──┐ │
         │CACHE OK │ │⚠️   │ │
         │✅ Live  │ │Old  │ │
         └─────────┘ └─────┘ │
                            ┌▼─────┐
                            │❌Off  │
                            │Offline│
                            └───────┘
```

#### ⏱️ Comportamento Temporale

| Scenario      | Azione                       | Badge                           |
| ------------- | ---------------------------- | ------------------------------- |
| Cache fresca  | Usa cache (0-10 min)         | **📦 Cache** (Verde)             |
| Cache scaduta | Contatta API per aggiornare  | **🌐 Live** (Blu)                |
| API offline   | Fallback sulla cache scaduta | **⚠️ Cache Vecchia** (Arancione) |
| Nessun dato   | Nessuna cache, API offline   | **❌ Offline** (Rosso)           |

#### 💾 Gestione della Cache

```javascript
// Cache ha una durata di 10 minuti
const CACHE_DURATION = 10 * 60 * 1000; // 10 minuti

// Quando una cache scade:
// 1. ✅ Se API è disponibile → scarica dati freschi
// 2. ⚠️  Se API è offline → usa cache scaduta come fallback
// 3. ❌ Se nessun dato disponibile → mostra errore offline

// Spazio storage utilizzato: ~5-10 MB (gestitoautomaticamente)
// Quando localStorage è pieno → pulizia automatica cache scadute
```

#### 🛠️ Utility da Console

Accedi ai comandi di controllo cache dalla **console del browser** (F12 → Console):

```javascript
// Svuota tutta la cache F1
clearF1Cache()
// Output: 🧹 Cache pulita: 15 elementi

// Visualizza statistiche cache
f1Stats()
// Output: 📊 Cache: 8 valide, 2 scadute (10 totali)
```

#### �📊 Endpoints Principali Utilizzati

| Endpoint      | Descrizione         | Esempio                     |
| ------------- | ------------------- | --------------------------- |
| `/meetings`   | Elenco gare         | `/meetings?year=2026`       |
| `/drivers`    | Elenco piloti       | `/drivers?year=2026`        |
| `/sessions`   | Sessioni di gara    | `/sessions?meeting_key=1`   |
| `/results`    | Risultati gara      | `/results?session_key=1`    |
| `/team_radio` | Comunicazioni radio | `/team_radio?session_key=1` |

### 🔄 Struttura Richieste HTTP

```javascript
// Esempio: Ottenere le gare del 2026
const year = 2026;
const racesUrl = `${API_BASE_URL}/meetings?year=${year}`;

fetch(racesUrl)
    .then(response => {
        if (!response.ok) throw new Error('Errore nella richiesta');
        return response.json();
    })
    .then(data => {
        console.log('Gare caricate:', data);
        // Elaborazione dati
        displayRaces(data);
    })
    .catch(error => {
        console.error('Errore API:', error);
        showErrorMessage('Impossibile caricare le gare');
    });
```

### 📝 Esempio di Risposta JSON

```json
{
  "meetings": [
    {
      "meeting_key": 1234,
      "year": 2026,
      "circuit_short_name": "BHR",
      "circuit_name": "Bahrain",
      "meeting_name": "Bahrain Grand Prix",
      "date": "2026-03-15",
      "location": "Sakhir"
    }
  ]
}
```

### ⚠️ Gestione Errori

```javascript
// Timeout personalizzato
const fetchWithTimeout = async (url, timeout = 5000) => {
    const controller = new AbortController();
    const id = setTimeout(() => controller.abort(), timeout);
    
    try {
        const response = await fetch(url, { signal: controller.signal });
        clearTimeout(id);
        return response.json();
    } catch (error) {
        if (error.name === 'AbortError') {
            throw new Error('Richiesta scaduta');
        }
        throw error;
    }
};
```

---

## 💡 Esempio di Utilizzo - Caricamento Gare

```javascript
// Caricamento gare della stagione 2026 con podio
async function loadRaces(year) {
    try {
        // 1. Fetch gare
        const meetingsResponse = await fetch(`${API}/meetings?year=${year}`);
        const meetings = await meetingsResponse.json();
        
        // 2. Per ogni gara, ottieni i risultati
        for (const meeting of meetings) {
            const sessionsResponse = await fetch(
                `${API}/sessions?meeting_key=${meeting.meeting_key}`
            );
            const sessions = await sessionsResponse.json();
            
            // 3. Estrai sessione gara (main race)
            const raceSession = sessions.find(s => s.session_type === 'Race');
            
            // 4. Ottieni i risultati
            const resultsResponse = await fetch(
                `${API}/results?session_key=${raceSession.session_key}`
            );
            const results = await resultsResponse.json();
            
            // 5. Estrai podio (top 3)
            const podium = results.slice(0, 3);
            
            // 6. Visualizza nel DOM
            displayRaceCard(meeting, podium);
        }
    } catch (error) {
        console.error('Errore nel caricamento:', error);
        showErrorState();
    }
}
```

### Screenshot

### Screenshot

<img src="img/sito/home.png" width="600">

*Homepage con ultime gare e classifica*

<br><br>

<img src="img/sito/gare.png" width="600">

*Pagina storico gare con selezione anno*

<br><br>

<table><tr>
<td><img src="img/sito/info_gara.png" width="400"></td>
<td><img src="img/sito/info_gara2.png" width="400"></td>
</tr></table>
*Dettagli gara con griglia e risultati*

<br><br>

<table><tr>
<td><img src="img/sito/classifiche.png" width="400"></td>
<td><img src="img/sito/classifiche2.png" width="400"></td>
</tr></table>

*Classifiche stagionali costruttori e piloti*

<br><br>

<img src="img/sito/dettagli.png" width="600">

*Dettagli su piloti e team*

<br><br>

<img src="img/sito/minigiochi.png" width="600">

*Minigiochi interattivi*

<br><br>

<img src="img/sito/login.png" width="600">

*Login per proteggere l'accesso ai team radio*

<br><br>

<table><tr>
<td><img src="img/sito/radio.png" width="400"></td>
<td><img src="img/sito/radio2.png" width="400"></td>
</tr></table>

*Team radio ascoltabili, divisi per gara*


---

## 📚 Risorse Utili

- **[OpenF1 API Documentation](https://openf1.org/docs/#api-endpoints)** - Documentazione ufficiale
- **[MDN - Fetch API](https://developer.mozilla.org/en-US/docs/Web/API/Fetch_API)** - Guida Fetch
- **[MDN - Async/Await](https://developer.mozilla.org/en-US/docs/Learn/JavaScript/Asynchronous/Promises)** - Guida Promises
- **[CSS Tricks - Responsive Design](https://css-tricks.com/snippets/css/a-guide-to-flexbox/)** - Guide CSS
- **[GitHub - Markdown Cheatsheet](https://github.com/adam-p/markdown-here/wiki/Markdown-Cheatsheet)** - Markdown

---

<div align="center">

🏁 **Buon divertimento con F1 API!** 🏁

</div>