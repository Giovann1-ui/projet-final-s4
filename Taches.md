
# V1
- [x] base
    - [x] conception base de donnees (Giovanni et Harena)
    - [x] migration de base de donnees
    - [x] seed de la base de donnees
    - [x] config de .env
- [ ] operateur (Giovanni)
    - [x] insertion des prefixes valables des operateurs
        - [x] page de creation d'un prefixe
            - [x] controller
            - [x] route
            - [x] vue
            - [x] modele
    - [x] page de situation des gains via les differents frais (retrait et transfert)
        - [?] c'est une page qui affiche le total de gains pour les retraits et transferts
        - [x] page
        - [x] controller
        - [x] route
        - [x] modele
    - [x] pages des situation des comptes clients
        - [?] on y montre le solde, les transactions
        - [x] page
            - [x] liste des clients
            - [ ] si on clique sur un client, on va vers une page de detail du client
                - [x] on y montre le solde du client, les transactions du client
            
        
- [ ] client (Harena)
    - [X]Login automatique par numero sans inscription prealable(AuthController, AuthFilter, CompteModel::getOrCreate, validation du préfixe)
    - [X]Modeles CompteModel et OperationModel (crédit/débit atomiques, historique)
    - [X]Librairie FraisService : calcul des frais par tranche, contrôle du solde,execution transactionnelle du depot / retrait / transfert
    - [X]Cote client : consultation du solde, depot, retrait, transfert,historiques filtrables (type + période) avec totaux
    - [x]Protection CSRF sur tous les formulaires + esc() sur toutes les sorties

# V2

- [x] base (Giovanni et Harena)
    - [x] ajout
        - [x] table des operateurs(id, nom, isUs, commission)
    - [x] modification
        - [x] table prefixe_operateur
            - [x] ajouter un foreign key de operateur_id
            - [x] ajouter colonne frais_retrait, commission
- [ ] operateur (Giovanni et Harena)
    - [x] Configuration des préfixes valable pour les autres opérateurs (ex: 032 et 031, …)
        - [x] modification de base deja faite
    - [x] Configuration % en plus de commissions pour les transferts vers les autres opérateurs
        - [x] modification de base deja faite
    - [x] page situationGain
        - [x] separer operateur et autres operateurs
            - [x] page
                <!-- - [ ] ajouter montant total des gains pour les retraits et transferts pour chaque operateur -->
                - [x] liste des operations RETRAIT et TRANSFERT pour chaque operateur
            - [x] route
            - [x] controller
            - [x] modele
    - [x] page de situation a envoyer a chaque operateur
        - [x] page
            - [x] creer la page
        - [x] controller
            - [x] creer le controller
            - [x] faire attention aux gestion de tableau associatif
        - [x] route

- [x] client (Harena)
    - [x] Option inclure frais de retrait lors de l’envoi
        - [x] modification table ajout colonnefrais_retrait
        - [x] modification vue checkbox
        - [x] controlleur ajout frais et exclusion autres operateurs 
            - il n’y a pas de frais de retrait pour les autres opérateurs
    - [x] Envoi multiple vers plusieurs numéros ( divisé le montant pour chaque numéro) même opérateur uniquement
        - [x] vue : une ligne d'input par numero (ajout/suppression en JS)
        - [x] controlleur + FraisService::transfererMultiple (tout ou rien, memeOperateur via operateur_id)

- [ ] promotion pourcentage de frais de retrait sur meme operateur
    - [x] creer table promotion
        - [x] id, promotion
    - [x] creer modele promotion
    - [ ] modifier la fonction dans le modele : transfere store