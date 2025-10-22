```mermaid
sequenceDiagram
title Recherche d’un logement et contact du loueur

participant E as Étudiant
participant I as Interface Web
participant S as Système DormQuest
participant L as Loueur

E->>I: Recherche un logement (ville, budget)
I->>S: Envoie la requête de recherche
S-->>I: Retourne la liste d'annonces correspondantes
E->>I: Sélectionne une annonce
I->>S: Demande les détails de l'annonce
S-->>I: Renvoie les infos (description, photos, contact)
E->>L: Envoie un message ou candidature
L-->>E: Répond à la demande
