```mermaid
flowchart LR
    %% Diagramme de "cas d'utilisation" simulé avec un flowchart Mermaid
    %% Thème : Fonctionnalités principales du site DormQuest

    %% === Acteurs ===
    A[👤 Étudiant]:::actor
    B[🏢 Loueur / Agence]:::actor

    %% === Zone du système ===
    subgraph S["Système DormQuest"]
        UC1[(Créer un compte)]
        UC2[(Se connecter / Se déconnecter)]
        UC3[(Modifier le profil)]
        UC4[(Mot de passe oublié)]

        UC5[(Rechercher un logement)]
        UC6[(Appliquer des filtres)]
        UC7[(Ajouter aux favoris)]
        UC8[(Contacter un loueur)]
        UC9[(Faire une demande de colocation)]
        UC10[(Consulter coordonnées)]

        UC11[(Déposer une annonce)]
        UC12[(Modifier / Supprimer une annonce)]
        UC13[(Gérer ses annonces via dashboard)]
        UC14[(Recevoir notification)]
    end

    %% === Relations Étudiant ===
    A --> UC1
    A --> UC2
    A --> UC3
    A --> UC4
    A --> UC5
    A --> UC6
    A --> UC7
    A --> UC8
    A --> UC9
    A --> UC10

    %% === Relations Loueur ===
    B --> UC1
    B --> UC2
    B --> UC11
    B --> UC12
    B --> UC13
    B --> UC14

    %% === Styles ===
    classDef actor fill:#f0f0f0,stroke:#333,stroke-width:1px,font-weight:bold,rx:10;
    classDef usecase fill:#e8f4ff,stroke:#0066cc,stroke-width:1px,rx:20;
    class S fill:#fdfdfd,stroke:#888,stroke-width:1.5px,rx:15;
