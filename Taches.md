<!-- ! sans caractere specila comm un accent -->
# V1
- [x] base
    - [x] conception base de donnees (Giovanni et Harena)
    - [x] migration de base de donnees
    - [x] seed de la base de donnees
    - [x] config de .env
- [ ] operateur (Giovanni)
    - CRUD des prefixes valables des operateurs
    - [ ] page de situation des gains via les differents frais (retrait et transfert)
        - [?] c'est une page qui affiche le total de gains pour les retraits et transferts
    - [ ] pages des situation des comptes clients
        - [?] on y montre le solde, les transactions
        
- [ ] client (Harena)
    - [ ]Login automatique par numero sans inscription prealable(AuthController, AuthFilter, CompteModel::getOrCreate, validation du préfixe)
    - [ ]Modeles CompteModel et OperationModel (crédit/débit atomiques, historique)
    - [ ]Librairie FraisService : calcul des frais par tranche, contrôle du solde,execution transactionnelle du depot / retrait / transfert
    - [ ]Cote client : consultation du solde, depot, retrait, transfert,historiques filtrables (type + période) avec totaux
    - [ ]Protection CSRF sur tous les formulaires + esc() sur toutes les sorties