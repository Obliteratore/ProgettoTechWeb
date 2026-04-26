### STRUTTURA HTML

###### 

###### -Elementi interattivi devono avere una grandezza minima di 44x44 px.



###### -Ogni pagina deve contenere il breadcrumb.

###### 

###### -Specificare le parole in lingua diversa da quella italiana tramite il seguente comando: `<span lang = [lingua]> [testo in lingua straniera] </span>`

###### 

###### -Consigliabile avere al massimo un tag h1 nell'header, e uno nel main.

###### 

###### -Usare tag `<time>` per le date, con attributo `datetime`.

###### 

###### -Usare i tag `<em>` e `<strong>` piuttosto che utilizzare i tag `<i>` (italico) e `<b>` (bold), siccome i primi hanno un significato semantico (il browser gli da più peso).

###### 

###### -Se il significato di un'abbreviazione è già scritto dopo l'abbreviazione stessa, mettere il tag `<abbr>` ma senza l'attributo `title`, per non farlo ripetere due volte.



###### -Per creare bottoni usare il tag `<button>`.



###### -Inserire all'interno del tag `<html>` l'attributo `title` con le keywords e la descirione del sito nell'attributo `description`. Controllare poi che tutte le parole chiave appaiano all'intero degli headers.





### IMMAGINI

###### 

###### -Usare JPG e WEBP (peso minore ma tempo di rendering maggiore rispetto a JPG) per immagini. Se sono immagini molto piccole va bene anche PNG (logo).

###### 

###### -Se ho molte immagini, specificare width e height nell'html per permette di velocizzare il rendering.

###### 

###### -Bilanciare il peso e la qualità delle immagini.

###### 

###### -Immagini devono avere peso <=1MB.

###### 

###### -Immagini solo decorative posso inserirle da CSS, quelle di contenuto da html.



###### -Definire il colore di background di default, simile al colore predominante dell'eventuale immagine di background, così se non si carica l'immagine il testo sarà ancora leggibile.





### ACCESSIBILITA'

###### 

###### -Usare Font accessibili.

###### -Per avere l'attributo `alt` delle immagini vuoto, scrivere solamente `alt=""`

###### 

###### -Mettere aria-label e gli altri attributi aria per gli screen reader.

###### 

###### -Mettere nell'header e nel menù un tag nav, con aria-label="Aiuti alla navigazione", mettendo il link al contenuto, con testo "Vai al contenuto". Eventualmente fare la stessa cosa per saltare dall'header al menù. Questi link dovranno essere invisibili di base, ma dovranno apparire quando il focus del tab ci arriva sopra.

###### 

###### -Se possibile link visitato in contrasto 3:1 con link NON visitato, se non si riesce scrivere in relazione che il tentativo è stato fatto.

###### 

###### -Contrasto tra background, testo, link in contrasto tra loro 4,5:1.



###### -Per ottenere maggiore contrasto conviene lavorare sulla luminosità dei colori.

###### 

###### -Per capire se mettere alt alle immagini, usare l'alt decision tree di W3.

###### 

###### -Se si hanno due link vicini che portano allo stesso posto, uno dei due non lo si fa leggere dallo screen reader con aria-hidden se è testo, o con role:presentation per un'immagine.



###### -Interlinea compreso tra 1 e (raccomandabile) 1.5.

###### 

###### -Sottolineare i link se possibile.



###### -Associare etichette alle label di input delle form, usare autocomplete se possibile, required dove necessario, e fornire esempi (meglio se sotto alla label, così non spariscono quando si inizia a scrivere).



###### -NON usare bandiere per scegliere la lingua.





### CELLULARE



###### -Permettere l'apertura del menù grazie a swipe da cellulare.



###### -Usare le gesture per il sito da mobile.





### ERRORI

###### 

###### -Gestire gli errori in modo appropriato, rassicurare l'utente, spiegare il problema, fornirgli un'indicazione su come continuare (es.: "Contatta il supporto"), eventualmente la gestione può essere simpatica. 



###### -Quando ho un errore in una label di input, se faccio apparire la scritta "Errore", di solito è sopra la label, e quindi uso aria-live=true per far tornare lo screen reader indietro a leggere "Errore", perché se no continua senza leggerlo, dal momento che viene prima della label.





### VARIE



###### -Per stampa rimuovere il menù, i link e le cose con interazione in generale, mentre il breadcrumb può essere utile.
