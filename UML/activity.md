```mermaid
flowchart TD
    A[Début] --> B[S'authentifier en tant que loueur]
    B --> C[Vérifier l'identité et le rôle]
    C -->|Valide| D[Accéder au dashboard]
    D --> E[Remplir le formulaire d'annonce]
    E --> F[Vérifier les champs obligatoires]
    F -->|OK| G[Uploader au moins 3 photos]
    G --> H[Valider et publier]
    H --> I[Annonce enregistrée en base]
    I --> J[Notification envoyée]
    J --> K[Fin]
    F -->|Erreur| E

