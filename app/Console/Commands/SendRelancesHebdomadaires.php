<?php

namespace App\Console\Commands;

use App\Models\CRMClient;
use App\Models\CoordonneesPersonnelles;
use App\Models\InformationsPersonnelles;
use App\Models\ProfilVisa;
use App\Models\SmsLog;
use App\Models\SmsRelanceAuto;
use App\Models\User;
use App\Services\OrangeSmsService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendRelancesHebdomadaires extends Command
{
    protected $signature   = 'sms:relances-hebdomadaires';
    protected $description = 'Envoie les relances SMS hebdomadaires aux clients selon leur statut';

    /**
     * Messages de relance par statut.
     * La clé est le statut exact du champ `statut` dans crm_clients.
     * [Prénom] sera remplacé dynamiquement.
     */
    public static array $messagesParStatut = [
        'En attente de paiement des frais de profil visa et d\'inscription' => [
            "Bonjour [Prénom], votre profil visa peut être créé dès réception des frais de profil et d'inscription. Paiement sécurisé : https://psiafrica.ci/paiement  +225 0104040405 – www.psiafrica.ci",
            "[Prénom], nous sommes en attente du règlement des frais de profil visa et d'inscription pour lancer votre dossier : https://psiafrica.ci/paiement  +225 0104040405",
            "Votre projet est bien enregistré chez PSI AFRICA. Merci de finaliser le paiement pour activer votre profil : https://psiafrica.ci/paiement  +225 0104040405 – www.psiafrica.ci",
            "Nous pouvons démarrer la création de votre profil visa immédiatement après validation des frais d'inscription : https://psiafrica.ci/paiement  +225 0104040405",
            "[Prénom], votre place est réservée. Merci d'effectuer le paiement des frais de profil et d'inscription pour lancer la procédure : https://psiafrica.ci/paiement  +225 0104040405",
            "Afin d'éviter tout retard dans votre projet, merci de procéder au règlement des frais de profil visa et d'inscription : https://psiafrica.ci/paiement  +225 0104040405",
            "Votre dossier est prêt à être lancé. La création de votre profil commence dès réception du paiement : https://psiafrica.ci/paiement +2250104040405 – www.psiafrica.ci",
            "[Prénom], plus tôt le paiement est effectué, plus vite votre profil visa sera traité : https://psiafrica.ci/paiement +2250104040405",
            "Nous gardons votre demande active pour le moment. Merci de confirmer votre engagement en réglant les frais : https://psiafrica.ci/paiement +2250104040405",
            "Votre projet peut avancer cette semaine dès validation des frais de profil et d'inscription : https://psiafrica.ci/paiement +2250104040405",
            "[Prénom], dès réception du paiement, un conseiller lancera immédiatement la création de votre profil visa : https://psiafrica.ci/paiement +2250104040405",
            "Votre accompagnement débute dès validation des frais de profil et d'inscription. Accès au paiement : https://psiafrica.ci/paiement +2250104040405 – www.psiafrica.ci",
            "Nous restons disponibles pour toute question concernant la procédure ou le paiement : https://psiafrica.ci/paiement +2250104040405",
            "Votre projet reste prioritaire chez PSI AFRICA. Merci de finaliser le règlement pour démarrer votre dossier : https://psiafrica.ci/paiement +2250104040405",
            "[Prénom], souhaitez-vous que nous maintenions votre demande active ? Le paiement permettra de lancer votre profil visa : https://psiafrica.ci/paiement +2250104040405",
            "Dernier rappel concernant votre dossier en attente. Sans validation des frais de profil et d'inscription, la procédure ne peut pas démarrer : https://psiafrica.ci/paiement +2250104040405",
            "Votre demande reste ouverte chez PSI AFRICA. Merci de confirmer votre engagement en procédant au paiement : https://psiafrica.ci/paiement +2250104040405",
            "[Prénom], nous pouvons lancer la création de votre profil visa immédiatement après réception du règlement : https://psiafrica.ci/paiement +2250104040405",
            "Sans retour de votre part, votre dossier pourrait être mis en attente prolongée. Paiement sécurisé ici : https://psiafrica.ci/paiement +2250104040405",
            "PSI AFRICA reste disponible pour activer votre profil visa et votre inscription dès validation des frais : https://psiafrica.ci/paiement +2250104040405 – www.psiafrica.ci",
        ],
        'RECHERCHE D\'EMPLOI EN COURS' => [
            "Votre dossier est toujours suivi activement. Les recruteurs prennent le temps nécessaire, mais nous restons à votre disposition pour tout ajustement. +225 0104040405 – www.psiafrica.ci",
            "[Prénom], votre recherche d'emploi est en cours. Les recruteurs décideront du calendrier, mais nous restons disponibles pour toute modification de projet ou visa. +225 0104040405 – www.psiafrica.ci",
            "Votre profil est en cours de traitement par notre équipe. Vous avez la possibilité d'adapter votre projet ou votre type de visa si besoin. +225 0104040405 – www.psiafrica.ci",
            "[Prénom], nous continuons à rechercher des opportunités pour vous. Vous pouvez changer votre projet ou visa et nous vous guiderons dans le nouveau choix. +225 0104040405 – www.psiafrica.ci",
            "La recherche d'emploi est active. Nous ne savons pas quand vous aurez un retour des recruteurs, mais votre projet reste flexible. +225 0104040405 – www.psiafrica.ci",
            "[Prénom], des opportunités sont en cours d'évaluation pour votre profil. Vous pouvez modifier votre type de visa ou projet à tout moment si nécessaire. +225 0104040405 – www.psiafrica.ci",
            "Bonjour [Prénom], nous recherchons activement un emploi correspondant à votre profil. Vous pouvez changer de type de visa ou de projet à tout moment, nous vous accompagnerons. +225 0104040405 – www.psiafrica.ci",
            "Nous recherchons actuellement un emploi correspondant à vos compétences. Vous pouvez changer de type de visa ou projet si votre situation évolue. +225 0104040405 – www.psiafrica.ci",
            "[Prénom], votre candidature est en cours auprès des recruteurs. Nous vous informerons de tout retour, et vous pouvez toujours réajuster votre projet ou visa. +225 0104040405 – www.psiafrica.ci",
            "La recherche d'emploi se poursuit. Si vous souhaitez modifier votre projet ou visa, nous sommes là pour vous accompagner dans ce choix. +225 0104040405 – www.psiafrica.ci",
            "[Prénom], la recherche de votre emploi est en cours. Les délais dépendent des recruteurs, mais vous pouvez adapter votre projet ou type de visa si nécessaire. +225 0104040405 – www.psiafrica.ci",
            "Votre profil reste actif dans nos recherches. N'hésitez pas à nous informer si vous souhaitez changer de type de visa ou d'option de projet. +225 0104040405 – www.psiafrica.ci",
            "[Prénom], des opportunités sont étudiées pour vous. La recherche est toujours en cours, mais nous sommes disponibles si vous souhaitez réajuster votre projet. +225 0104040405 – www.psiafrica.ci",
            "La sélection des recruteurs prend du temps. Votre profil est suivi activement et vous pouvez changer de projet ou visa à tout moment. +225 0104040405 – www.psiafrica.ci",
            "[Prénom], la recherche se poursuit. Nous vous accompagnerons si vous décidez de modifier votre type de visa ou projet pendant ce processus. +225 0104040405 – www.psiafrica.ci",
            "[Prénom], votre profil est toujours en recherche active d'emploi. Nous ne pouvons pas prévoir les délais, mais nous vous aiderons si vous souhaitez changer de visa ou de projet. +225 0104040405 – www.psiafrica.ci",
            "Votre dossier est suivi. Les recruteurs prendront leur décision, mais votre projet reste flexible et modifiable avec notre accompagnement. +225 0104040405 – www.psiafrica.ci",
            "[Prénom], nous continuons à chercher des opportunités pour vous. Vous pouvez ajuster votre type de visa ou projet à tout moment et nous vous guiderons. +225 0104040405 – www.psiafrica.ci",
            "La recherche est en cours, mais nous ne pouvons pas garantir un délai précis. Vous avez toujours la possibilité de changer de projet ou visa. +225 0104040405 – www.psiafrica.ci",
            "[Prénom], nous restons mobilisés pour vous trouver un emploi. Vous pouvez réajuster votre projet ou visa, et nous vous accompagnerons dans ce choix. +225 0104040405 – www.psiafrica.ci",
        ],
        'Lead' => [
            "Bonjour [Prénom], merci pour votre intérêt pour PSI Africa. Nous vous accompagnons pour vos projets de visa et voyage. Infos : www.psiafrica.ci | +225 0104040405",
            "Votre projet de voyage mérite une bonne préparation. PSI Africa vous conseille gratuitement. Contactez-nous : www.psiafrica.ci | +225 0104040405",
            "Saviez-vous que nos experts augmentent vos chances d'obtenir un visa ? Parlons de votre projet : www.psiafrica.ci | +225 0104040405",
            "Visa, billet d'avion, assurance… PSI Africa vous accompagne de A à Z. Infos : www.psiafrica.ci | +225 0104040405",
            "Ne laissez pas passer votre chance de voyager cette année. Un conseiller peut vous orienter. www.psiafrica.ci | +225 0104040405",
            "Beaucoup de nos clients réalisent leur projet grâce à un bon accompagnement. Pourquoi pas vous ? www.psiafrica.ci | +225 0104040405",
            "Avez-vous déjà votre projet de destination ? France, USA, Canada… Nous vous guidons. www.psiafrica.ci | +225 0104040405",
            "Un bon dossier fait toute la différence pour un visa. PSI Africa vous aide à bien vous préparer. www.psiafrica.ci | +225 0104040405",
            "Conseil gratuit, suivi sérieux et assistance complète. Lancez votre projet dès maintenant. www.psiafrica.ci | +225 0104040405",
            "Le moment idéal pour commencer votre demande de visa, c'est maintenant. Contactez-nous : www.psiafrica.ci | +225 0104040405",
            "Vous hésitez encore ? Nos conseillers répondent à toutes vos questions. www.psiafrica.ci | +225 0104040405",
            "PSI Africa, votre partenaire pour visa, voyage et installation à l'étranger. Infos : www.psiafrica.ci | +225 0104040405",
            "De nombreux candidats nous font confiance chaque mois. Rejoignez-les ! www.psiafrica.ci | +225 0104040405",
            "Préparez votre avenir dès aujourd'hui. Un simple échange peut tout changer. www.psiafrica.ci | +225 0104040405",
            "Billets d'avion au meilleur prix + assistance visa complète. Parlons-en : www.psiafrica.ci | +225 0104040405",
            "Votre projet mérite un accompagnement sérieux et professionnel. PSI Africa est là pour vous. www.psiafrica.ci | +225 0104040405",
            "N'attendez pas la dernière minute pour votre demande de visa. Commencez maintenant. www.psiafrica.ci | +225 0104040405",
            "Un projet de voyage en famille, tourisme ou travail ? Nous vous orientons. www.psiafrica.ci | +225 0104040405",
            "Votre réussite est notre priorité. Discutons de votre projet avec un conseiller. www.psiafrica.ci | +225 0104040405",
            "Dernier rappel : êtes-vous prêt à lancer votre projet de visa ? PSI Africa vous accompagne. www.psiafrica.ci | +225 0104040405",
        ],
        'Prospect' => [
            "Bonjour [Prénom], suite à votre demande d'information, PSI Africa est prêt à vous accompagner pour votre projet de visa. Infos : www.psiafrica.ci | +225 0104040405",
            "Avez-vous déjà choisi votre destination ? France, USA, Canada… Nos conseillers peuvent vous orienter. www.psiafrica.ci | +225 0104040405",
            "Un bon projet commence par de bons conseils. Profitez de notre accompagnement. www.psiafrica.ci | +225 0104040405",
            "PSI Africa vous aide pour votre visa, billet d'avion et assurance voyage. Parlons-en : www.psiafrica.ci | +225 0104040405",
            "Chaque jour compte pour préparer un bon dossier visa. Lancez-vous maintenant. www.psiafrica.ci | +225 0104040405",
            "Beaucoup hésitent, mais ceux qui passent à l'action avancent. Et vous ? www.psiafrica.ci | +225 0104040405",
            "Nos experts vous expliquent clairement les démarches à suivre pour voyager sereinement. www.psiafrica.ci | +225 0104040405",
            "Un projet de tourisme, travail ou visite familiale ? PSI Africa vous guide. www.psiafrica.ci | +225 0104040405",
            "Ne restez pas avec vos questions. Un conseiller peut vous répondre rapidement. www.psiafrica.ci | +225 0104040405",
            "Commencer tôt augmente vos chances de réussite pour votre visa. Contactez-nous : www.psiafrica.ci | +225 0104040405",
            "Votre projet est peut-être plus simple que vous ne le pensez. Discutons-en. www.psiafrica.ci | +225 0104040405",
            "PSI Africa accompagne chaque année de nombreux voyageurs vers leur destination. www.psiafrica.ci | +225 0104040405",
            "Un accompagnement sérieux peut faire toute la différence dans votre demande de visa. www.psiafrica.ci | +225 0104040405",
            "C'est le bon moment pour préparer votre voyage en toute tranquillité. Infos : www.psiafrica.ci | +225 0104040405",
            "Besoin d'aide pour comprendre les démarches ? Nos conseillers sont disponibles. www.psiafrica.ci | +225 0104040405",
            "PSI Africa, votre partenaire pour concrétiser votre projet de voyage à l'étranger. www.psiafrica.ci | +225 0104040405",
            "Plus vous préparez tôt, plus vous êtes prêt le moment venu. Lancez votre projet. www.psiafrica.ci | +225 0104040405",
            "Un simple échange peut vous aider à y voir plus clair sur votre projet de visa. www.psiafrica.ci | +225 0104040405",
            "Vous êtes toujours intéressé par votre projet de voyage ? Nous restons disponibles. www.psiafrica.ci | +225 0104040405",
            "Dernier message : si votre projet est toujours d'actualité, PSI Africa est prêt à vous accompagner. www.psiafrica.ci | +225 0104040405",
        ],
        'Visa refusé' => [
            "Bonjour [Prénom], votre visa a été refusé. Vous avez la possibilité de relancer une nouvelle demande ou de déposer une réclamation auprès de l'ambassade. +225 0104040405 – www.psiafrica.ci",
            "[Prénom], nous pouvons vous accompagner pour une nouvelle demande de visa ou pour effectuer une réclamation officielle auprès de l'ambassade. +225 0104040405 – www.psiafrica.ci",
            "Votre refus de visa n'est pas une fin. Vous pouvez relancer la procédure ou contester la décision auprès des services consulaires avec notre accompagnement. +225 0104040405 – www.psiafrica.ci",
            "[Prénom], il est possible de redemander un visa ou de faire une réclamation auprès de l'ambassade. Nous vous guidons dans toutes les démarches. +225 0104040405 – www.psiafrica.ci",
            "Votre dossier peut être relancé. Contactez-nous pour préparer une nouvelle demande de visa ou une réclamation officielle. +225 0104040405 – www.psiafrica.ci",
            "[Prénom], un refus n'empêche pas de tenter à nouveau. Nous pouvons relancer votre demande de visa ou déposer une réclamation. +225 0104040405 – www.psiafrica.ci",
            "Votre situation peut évoluer. Si vous souhaitez relancer le visa ou faire une réclamation auprès de l'ambassade, nous sommes là pour vous accompagner. +225 0104040405 – www.psiafrica.ci",
            "[Prénom], nous analysons votre dossier pour vous proposer la meilleure stratégie : relance de visa ou recours auprès de l'ambassade. +225 0104040405 – www.psiafrica.ci",
            "Un refus peut être contourné avec une nouvelle demande ou une réclamation. Nous vous guidons étape par étape. +225 0104040405 – www.psiafrica.ci",
            "[Prénom], vous pouvez toujours relancer votre projet de visa. Nous préparons avec vous la nouvelle demande ou la réclamation. +225 0104040405 – www.psiafrica.ci",
            "[Prénom], votre refus n'est pas définitif. Nous pouvons relancer le visa ou déposer une réclamation auprès de l'ambassade. +225 0104040405 – www.psiafrica.ci",
            "Votre dossier peut être optimisé pour une nouvelle demande ou une contestation officielle. Contactez-nous pour les démarches. +225 0104040405 – www.psiafrica.ci",
            "[Prénom], nous restons disponibles pour relancer votre visa ou effectuer une réclamation officielle afin de maximiser vos chances. +225 0104040405 – www.psiafrica.ci",
            "Chaque refus peut être analysé pour corriger les points bloquants. Vous pouvez relancer le visa ou déposer une réclamation. +225 0104040405 – www.psiafrica.ci",
            "[Prénom], ne laissez pas un refus arrêter votre projet. Nous vous accompagnons pour relancer votre demande ou faire une réclamation. +225 0104040405 – www.psiafrica.ci",
            "[Prénom], votre dossier reste éligible pour une nouvelle demande de visa ou pour une réclamation auprès de l'ambassade. +225 0104040405 – www.psiafrica.ci",
            "Le refus initial peut être contesté ou corrigé. Contactez-nous pour relancer votre visa ou déposer une réclamation. +225 0104040405 – www.psiafrica.ci",
            "[Prénom], nous pouvons préparer une nouvelle demande ou une réclamation officielle pour maximiser vos chances de réussite. +225 0104040405 – www.psiafrica.ci",
            "Votre projet de visa n'est pas terminé. Relance ou réclamation possible avec notre accompagnement complet. +225 0104040405 – www.psiafrica.ci",
            "[Prénom], dernière chance de relancer votre demande ou de déposer une réclamation auprès de l'ambassade. Nous vous guidons pour chaque étape. +225 0104040405 – www.psiafrica.ci",
        ],
        'Visa accepté' => [
            "Bonjour [Prénom], félicitations pour l'obtention de votre visa ! Nous vous invitons à réserver votre billet d'avion et votre assurance avec PSI AFRICA pour respecter les dates et exigences du pays. +225 0104040405 – www.psiafrica.ci",
            "[Prénom], votre visa est accepté ! Pour éviter tout retard, nous vous recommandons de prendre votre billet et assurance avec nous et de consulter notre interprétation des exigences du pays. +225 0104040405 – www.psiafrica.ci",
            "Félicitations [Prénom] ! Votre visa est validé. Nous pouvons vous accompagner pour réserver le vol, souscrire l'assurance et comprendre toutes les obligations liées à votre visa. +225 0104040405 – www.psiafrica.ci",
            "Votre visa est accepté. Pour garantir un départ conforme aux règles du pays, réservez votre billet et assurance avec nous et bénéficiez de notre interprétation du visa. +225 0104040405 – www.psiafrica.ci",
            "[Prénom], félicitations ! Nous vous guidons pour l'achat du billet, l'assurance et le respect des dates et exigences de votre visa. Contactez-nous dès maintenant. +225 0104040405 – www.psiafrica.ci",
            "[Prénom], votre visa est prêt ! Pour un départ serein, réservez votre billet d'avion et assurance avec PSI AFRICA et respectez les délais et obligations de votre visa. +225 0104040405 – www.psiafrica.ci",
            "Félicitations encore [Prénom] ! Ne perdez pas de temps : prenez votre billet et assurance avec nous et profitez de notre interprétation complète du visa. +225 0104040405 – www.psiafrica.ci",
            "Votre visa est accepté. Pour éviter tout souci, nous vous conseillons de réserver votre voyage et assurance avec PSI AFRICA et de vérifier toutes les exigences du pays. +225 0104040405 – www.psiafrica.ci",
            "[Prénom], assurez votre départ en toute sécurité : billet, assurance et interprétation des exigences de votre visa avec PSI AFRICA. +225 0104040405 – www.psiafrica.ci",
            "Félicitations [Prénom] ! Votre visa est validé. Nous vous accompagnons pour planifier votre départ en respectant toutes les dates et obligations. +225 0104040405 – www.psiafrica.ci",
            "[Prénom], avez-vous réservé votre billet et assurance ? Nous pouvons vous guider pour respecter toutes les dates et exigences de votre visa. +225 0104040405 – www.psiafrica.ci",
            "Votre visa est en main. PSI AFRICA peut vous accompagner pour acheter le billet, souscrire l'assurance et interpréter votre visa pour un départ sans problème. +225 0104040405 – www.psiafrica.ci",
            "[Prénom], partez en toute sérénité ! Réservez votre vol et assurance avec nous et bénéficiez de notre assistance pour comprendre votre visa et ses obligations. +225 0104040405 – www.psiafrica.ci",
            "Pour éviter tout retard ou complication, prenez votre billet et assurance dès maintenant avec PSI AFRICA et suivez nos recommandations pour votre visa. +225 0104040405 – www.psiafrica.ci",
            "[Prénom], félicitations ! Votre visa est prêt. Nous vous aidons à planifier le voyage correctement pour respecter toutes les obligations et dates légales. +225 0104040405 – www.psiafrica.ci",
            "[Prénom], votre départ peut être optimisé. Billet, assurance et interprétation du visa avec PSI AFRICA pour respecter toutes les obligations. +225 0104040405 – www.psiafrica.ci",
            "Félicitations [Prénom] ! N'attendez pas pour réserver votre voyage et assurance et assurez-vous de respecter toutes les exigences liées à votre visa. +225 0104040405 – www.psiafrica.ci",
            "Votre visa est validé. Nous restons disponibles pour vous accompagner dans la réservation de votre vol, assurance et interprétation complète du visa. +225 0104040405 – www.psiafrica.ci",
            "[Prénom], pour un départ serein et conforme, réservez votre billet et assurance avec PSI AFRICA et suivez nos conseils pour respecter toutes les exigences. +225 0104040405 – www.psiafrica.ci",
            "Félicitations [Prénom] ! Votre projet est prêt à démarrer. Nous vous guidons pour un voyage conforme, sûr et respectueux de toutes les obligations de votre visa. +225 0104040405 – www.psiafrica.ci",
        ],
        'En attente de décision visa' => [
            "Bonjour [Prénom], votre demande de visa est en cours de traitement par l'ambassade. Nous vous souhaitons bonne chance et restons disponibles pour vous accompagner, quel que soit le résultat. +225 0104040405 – www.psiafrica.ci",
            "[Prénom], votre visa est en attente de décision. PSI AFRICA reste à vos côtés pour vous conseiller et vous accompagner à chaque étape. +225 0104040405 – www.psiafrica.ci",
            "Votre demande de visa est suivie par l'ambassade. Nous vous souhaitons le meilleur et restons disponibles pour vous soutenir dans la suite de votre projet. +225 0104040405 – www.psiafrica.ci",
            "[Prénom], même si PSI AFRICA ne peut influencer la décision, nous restons à votre disposition pour toute question ou accompagnement futur. +225 0104040405 – www.psiafrica.ci",
            "Nous suivons votre dossier avec attention. Bonne chance pour votre visa ! Nous restons disponibles pour vous aider, quel que soit le résultat. +225 0104040405 – www.psiafrica.ci",
            "[Prénom], votre dossier est toujours en attente de décision. Nous vous souhaitons bonne chance et restons à votre disposition pour vous conseiller pour vos prochaines démarches. +225 0104040405 – www.psiafrica.ci",
            "PSI AFRICA suit votre dossier de près. Bonne chance pour votre visa ! Nous sommes là pour vous accompagner, quelle que soit l'issue. +225 0104040405 – www.psiafrica.ci",
            "[Prénom], votre demande est en cours à l'ambassade. Nous restons disponibles pour répondre à vos questions ou vous guider dans la suite de votre projet. +225 0104040405 – www.psiafrica.ci",
            "Votre visa est en attente de décision. Nous vous souhaitons le meilleur et restons disponibles pour toute assistance future. +225 0104040405 – www.psiafrica.ci",
            "[Prénom], nous espérons que votre demande sera favorable. PSI AFRICA reste à vos côtés pour vous conseiller ou relancer votre projet selon le résultat. +225 0104040405 – www.psiafrica.ci",
            "[Prénom], votre dossier est toujours en cours. Bonne chance pour votre visa ! Nous restons à votre disposition pour vous guider dans toutes les options disponibles. +225 0104040405 – www.psiafrica.ci",
            "PSI AFRICA suit votre demande de visa. Nous vous souhaitons bonne chance et restons disponibles pour toute question ou accompagnement futur. +225 0104040405 – www.psiafrica.ci",
            "[Prénom], même si nous ne pouvons pas influer sur la décision, nous restons disponibles pour vous aider à planifier la suite de votre projet. +225 0104040405 – www.psiafrica.ci",
            "Votre visa est en attente. Nous espérons une réponse favorable et restons à vos côtés pour vous soutenir dans la suite de votre démarche. +225 0104040405 – www.psiafrica.ci",
            "[Prénom], nous restons mobilisés pour vous accompagner selon le résultat de votre visa et pour toute question relative à votre projet. +225 0104040405 – www.psiafrica.ci",
            "[Prénom], votre demande est toujours en cours de traitement. Nous vous souhaitons le meilleur et restons disponibles pour vous guider selon l'issue de votre visa. +225 0104040405 – www.psiafrica.ci",
            "PSI AFRICA suit votre dossier attentivement. Quelle que soit la décision, nous restons disponibles pour vous conseiller et vous accompagner. +225 0104040405 – www.psiafrica.ci",
            "[Prénom], nous espérons que votre demande sera favorable. PSI AFRICA reste à vos côtés pour toutes les étapes futures. +225 0104040405 – www.psiafrica.ci",
            "Votre visa est en cours de traitement. Nous vous souhaitons bonne chance et restons disponibles pour vous guider dans vos prochaines démarches. +225 0104040405 – www.psiafrica.ci",
            "[Prénom], restez confiant pour votre visa ! PSI AFRICA est disponible pour vous accompagner, quelle que soit l'issue. +225 0104040405 – www.psiafrica.ci",
        ],
        'En attente de paiement des frais de cabinet' => [
            "Bonjour [Prénom], votre dossier est prêt à être lancé. Merci de finaliser les frais d'assistance ici : https://psiafrica.ci/paiement +225 0104040405 – www.psiafrica.ci",
            "[Prénom], nous sommes en attente du règlement pour activer votre procédure. Paiement sécurisé : https://psiafrica.ci/paiement +225 0104040405 – www.psiafrica.ci",
            "Votre projet est bien enregistré chez PSI AFRICA. Le paiement des frais permettra de démarrer votre accompagnement : https://psiafrica.ci/paiement +225 0104040405",
            "Nous restons disponibles pour finaliser votre inscription. Procédez au règlement ici : https://psiafrica.ci/paiement +225 0104040405 – www.psiafrica.ci",
            "[Prénom], votre place est toujours réservée. Merci d'effectuer le paiement pour lancer votre dossier : https://psiafrica.ci/paiement +225 0104040405",
            "Afin d'éviter tout retard dans votre projet, nous vous invitons à finaliser le paiement des frais ici : https://psiafrica.ci/paiement +225 0104040405",
            "Votre dossier est en attente de validation administrative. Règlement rapide et sécurisé : https://psiafrica.ci/paiement +225 0104040405 – www.psiafrica.ci",
            "[Prénom], plus tôt le paiement est effectué, plus vite votre procédure démarre : https://psiafrica.ci/paiement +225 0104040405",
            "Nous gardons votre dossier actif pour le moment. Merci de confirmer votre engagement ici : https://psiafrica.ci/paiement +225 0104040405",
            "Votre projet peut avancer cette semaine après validation des frais d'assistance : https://psiafrica.ci/paiement +225 0104040405 – www.psiafrica.ci",
            "[Prénom], dès réception des frais, un conseiller sera dédié à votre dossier. Paiement ici : https://psiafrica.ci/paiement +225 0104040405",
            "Votre accompagnement personnalisé commence dès validation du règlement : https://psiafrica.ci/paiement +225 0104040405 – www.psiafrica.ci",
            "Nous restons disponibles pour toute question sur les modalités de paiement. Accès direct : https://psiafrica.ci/paiement +225 0104040405",
            "Votre projet est toujours prioritaire chez PSI AFRICA. Merci de finaliser le règlement ici : https://psiafrica.ci/paiement +225 0104040405",
            "[Prénom], souhaitez-vous que nous maintenions votre dossier actif ? Paiement sécurisé : https://psiafrica.ci/paiement +225 0104040405",
            "Dernier rappel concernant votre dossier en attente. Validation des frais ici : https://psiafrica.ci/paiement +225 0104040405 – www.psiafrica.ci",
            "Votre demande reste ouverte chez PSI AFRICA. Merci de confirmer votre engagement : https://psiafrica.ci/paiement +225 0104040405",
            "[Prénom], nous pouvons lancer votre accompagnement immédiatement après réception du paiement : https://psiafrica.ci/paiement +225 0104040405",
            "Sans validation des frais, votre dossier pourrait être mis en attente prolongée. Paiement ici : https://psiafrica.ci/paiement +225 0104040405",
            "PSI AFRICA reste disponible pour activer votre projet dès réception du règlement : https://psiafrica.ci/paiement +225 0104040405 – www.psiafrica.ci",
        ],
    ];

    /**
     * Messages pour les candidats qui ont un profil visa créé mais non terminé (etape < 6).
     * [Prénom] sera remplacé dynamiquement.
     */
    protected array $messagesProfilVisaIncomplet = [
        "Bonjour [Prénom], votre profil visa est presque complet ! Terminez-le dès maintenant pour avancer dans votre projet. Besoin d'aide ? Contactez-nous : +2250104040405 | www.psiafrica.ci",
        "[Prénom], n'oubliez pas de finaliser votre questionnaire de profil visa. Notre équipe est là pour vous aider à chaque étape. +2250104040405 | www.psiafrica.ci",
        "Bonjour [Prénom], votre profil visa n'est pas encore terminé. Terminez-le aujourd'hui et rapprochez-vous de votre rêve de voyage ! Assistance : +2250104040405 | www.psiafrica.ci",
        "[Prénom], avez-vous rencontré une difficulté avec votre questionnaire ? Nous pouvons vous guider pour le compléter rapidement. +2250104040405 | www.psiafrica.ci",
        "Bonjour [Prénom], votre projet de visa mérite d'avancer ! Terminez votre profil maintenant et profitez de notre support complet. +2250104040405 | www.psiafrica.ci",
        "[Prénom], votre profil visa est en attente. Quelques minutes suffisent pour le finaliser. Besoin d'aide ? Nous sommes disponibles : +2250104040405 | www.psiafrica.ci",
        "Bonjour [Prénom], chaque détail compte pour votre visa. N'attendez plus pour finir votre profil. Contactez-nous si besoin : +2250104040405 | www.psiafrica.ci",
        "[Prénom], terminer votre questionnaire de profil visa vous rapproche de votre projet. Notre équipe peut vous assister à tout moment. +2250104040405 | www.psiafrica.ci",
        "Bonjour [Prénom], votre profil visa n'est qu'à quelques clics d'être complet ! Nous pouvons vous guider si nécessaire. +2250104040405 | www.psiafrica.ci",
        "[Prénom], ne laissez pas votre profil visa en attente. Terminez-le dès aujourd'hui et évitez les retards dans votre dossier. Assistance : +2250104040405 | www.psiafrica.ci",
        "Bonjour [Prénom], besoin d'un petit coup de main pour compléter votre profil visa ? Nous sommes là pour vous aider ! +2250104040405 | www.psiafrica.ci",
        "[Prénom], votre profil visa est important pour votre voyage. Prenez quelques minutes pour le compléter. Nous sommes à votre disposition : +2250104040405 | www.psiafrica.ci",
        "Bonjour [Prénom], avancer sur votre profil visa, c'est avancer vers votre rêve. Terminez-le maintenant avec notre aide si besoin. +2250104040405 | www.psiafrica.ci",
        "[Prénom], chaque étape de votre profil visa compte. N'hésitez pas à nous contacter pour toute assistance. +2250104040405 | www.psiafrica.ci",
        "Bonjour [Prénom], votre profil visa est incomplet. Finissez-le pour éviter tout retard dans votre dossier. Support : +2250104040405 | www.psiafrica.ci",
        "[Prénom], nous avons remarqué que votre profil visa n'est pas terminé. Quelques minutes suffisent pour avancer ! +2250104040405 | www.psiafrica.ci",
        "Bonjour [Prénom], pour que votre projet de visa progresse, terminez votre questionnaire. Assistance complète disponible : +2250104040405 | www.psiafrica.ci",
        "[Prénom], votre profil visa mérite d'être finalisé. Nous sommes disponibles pour vous aider à tout moment. +2250104040405 | www.psiafrica.ci",
        "Bonjour [Prénom], ne laissez pas votre profil visa en pause. Finalisez-le aujourd'hui avec notre aide si besoin. +2250104040405 | www.psiafrica.ci",
        "[Prénom], chaque jour compte pour votre projet de visa. Terminez votre profil maintenant et contactez-nous si vous avez besoin d'assistance : +2250104040405 | www.psiafrica.ci",
    ];

    public function handle(OrangeSmsService $smsService): int
    {
        $this->info('=== Relances hebdomadaires SMS - Démarrage ===');
        Log::info('SendRelancesHebdomadaires: Démarrage');

        $totalEnvoye  = 0;
        $totalEchec   = 0;
        $totalIgnore  = 0;

        foreach ($this->messagesParStatut as $statut => $messages) {
            $this->info("Traitement statut : {$statut}");

            // Récupérer les clients CRM avec ce statut
            $clients = CRMClient::where('statut', $statut)
                ->whereNotNull('contact')
                ->get();

            $this->info("  → {$clients->count()} client(s) trouvé(s)");

            foreach ($clients as $client) {
                $phone = $this->extractFirstPhone($client->contact ?? '');
                if (empty($phone)) {
                    $totalIgnore++;
                    continue;
                }

                // Récupérer ou créer l'entrée de relance automatique
                $relance = SmsRelanceAuto::firstOrCreate(
                    ['client_id' => $client->id, 'statut' => $statut],
                    [
                        'message_index'     => 0,
                        'status_changed_at' => $client->updated_at ?? now(),
                        'active'            => true,
                    ]
                );

                // Vérifier si actif
                if (!$relance->active) {
                    $totalIgnore++;
                    continue;
                }

                // Vérifier si 7 jours écoulés depuis dernier envoi
                if (!$relance->isDueForRelance()) {
                    $totalIgnore++;
                    continue;
                }

                // Construire le message
                $index   = $relance->message_index % count($messages);
                $message = $this->buildMessage($messages[$index], $client);

                // Envoyer le SMS
                $result = $smsService->sendSms($phone, $message);

                // Logger dans sms_logs
                SmsLog::create([
                    'sent_by'        => null,
                    'recipient_name' => trim(($client->prenoms ?? '') . ' ' . ($client->nom ?? '')),
                    'recipient_phone' => $phone,
                    'message'        => $message,
                    'status'         => $result['success'] ? 'sent' : 'failed',
                    'error_message'  => $result['success'] ? null : ($result['message'] ?? 'Erreur inconnue'),
                    'api_response'   => isset($result['data']) ? json_encode($result['data']) : null,
                    'sent_at'        => $result['success'] ? now() : null,
                ]);

                if ($result['success']) {
                    // Avancer l'index (reprend au début après le dernier message)
                    $nextIndex = ($relance->message_index + 1) % count($messages);
                    $relance->update([
                        'message_index' => $nextIndex,
                        'last_sent_at'  => now(),
                        'total_sent'    => $relance->total_sent + 1,
                    ]);

                    $msgNum = $index + 1;
                    $this->info("  ✓ SMS envoyé à {$client->prenoms} {$client->nom} ({$phone}) - Message #{$msgNum}");
                    Log::info("Relance envoyée : {$client->id} - {$phone} - statut: {$statut} - msg#{$index}");
                    $totalEnvoye++;
                } else {
                    $this->warn("  ✗ Échec pour {$client->prenoms} {$client->nom} ({$phone}) : " . ($result['message'] ?? ''));
                    Log::warning("Relance échouée : {$client->id} - {$phone}", $result);
                    $totalEchec++;
                }

                // Pause courte entre envois pour ne pas surcharger l'API
                usleep(500000); // 0.5 seconde
            }
        }

        // Traiter les profils visa incomplets
        [$e, $ec, $i] = $this->handleProfilVisaIncomplet($smsService);
        $totalEnvoye += $e;
        $totalEchec  += $ec;
        $totalIgnore += $i;

        $this->info("=== Terminé : {$totalEnvoye} envoyé(s), {$totalEchec} échec(s), {$totalIgnore} ignoré(s) ===");
        Log::info("SendRelancesHebdomadaires terminé : envoyés={$totalEnvoye}, échecs={$totalEchec}, ignorés={$totalIgnore}");

        return Command::SUCCESS;
    }

    /**
     * Relances pour les candidats ayant un profil visa créé mais non terminé (etape 1-5).
     */
    protected function handleProfilVisaIncomplet(OrangeSmsService $smsService): array
    {
        $statut   = 'PROFIL_VISA_INCOMPLET';
        $messages = $this->messagesProfilVisaIncomplet;

        $this->info("Traitement : Profils visa incomplets (etape < 6)");

        $profils = ProfilVisa::where('etape', '>=', 1)
            ->where('etape', '<', 6)
            ->get();

        $this->info("  → {$profils->count()} profil(s) trouvé(s)");

        $envoye = $echec = $ignore = 0;

        foreach ($profils as $profil) {
            // Récupérer le numéro de téléphone
            $coordonnees = CoordonneesPersonnelles::where('id_profil_visa', $profil->id)->first();
            $phone = trim($coordonnees->contact ?? '');

            // Fallback sur le compte utilisateur
            if (empty($phone) && $profil->user1d) {
                $user  = User::find($profil->user1d);
                $phone = trim($user->contact ?? '');
            }

            if (empty($phone)) {
                $ignore++;
                continue;
            }

            // Récupérer prénom, nom et sexe
            $infos  = InformationsPersonnelles::where('id_profil_visa', $profil->id)->first();
            $prenom = trim($infos->prenom ?? '');
            $nom    = trim($infos->nom ?? '');
            $sexe   = trim($infos->sexe ?? '');

            if ((empty($prenom) || empty($nom)) && $profil->user1d) {
                $user   = $user ?? User::find($profil->user1d);
                $parts  = explode(' ', trim($user->name ?? ''));
                if (empty($prenom)) $prenom = $parts[0] ?? '';
                if (empty($nom))    $nom    = $parts[1] ?? '';
            }

            // Déterminer la civilité selon le sexe
            if ($sexe === 'Féminin') {
                $civilite = 'Mme';
            } elseif ($sexe === 'Masculin') {
                $civilite = 'M.';
            } else {
                $civilite = 'M/Mme';
            }

            $salutation = trim($civilite . ' ' . $nom . ($prenom ? ' ' . $prenom : ''));
            if (empty(trim($nom . $prenom))) {
                $salutation = 'Client';
            }

            // Récupérer ou créer l'entrée de relance (client_id = profil_visa.id)
            $relance = SmsRelanceAuto::firstOrCreate(
                ['client_id' => $profil->id, 'statut' => $statut],
                [
                    'message_index'     => 0,
                    'status_changed_at' => $profil->updated_at ?? now(),
                    'active'            => true,
                ]
            );

            if (!$relance->active) {
                $ignore++;
                continue;
            }

            if (!$relance->isDueForRelance(1)) { // 24h pour profils visa incomplets
                $ignore++;
                continue;
            }

            // Construire et envoyer le message
            $index   = $relance->message_index % count($messages);
            $message = str_replace('[Prénom]', $salutation, $messages[$index]);

            $result = $smsService->sendSms($phone, $message);

            SmsLog::create([
                'sent_by'         => null,
                'recipient_name'  => $salutation,
                'recipient_phone' => $phone,
                'message'         => $message,
                'status'          => $result['success'] ? 'sent' : 'failed',
                'error_message'   => $result['success'] ? null : ($result['message'] ?? 'Erreur inconnue'),
                'api_response'    => isset($result['data']) ? json_encode($result['data']) : null,
                'sent_at'         => $result['success'] ? now() : null,
            ]);

            if ($result['success']) {
                $nextIndex = ($relance->message_index + 1) % count($messages);
                $relance->update([
                    'message_index' => $nextIndex,
                    'last_sent_at'  => now(),
                    'total_sent'    => $relance->total_sent + 1,
                ]);

                $msgNum = $index + 1;
                $this->info("  ✓ SMS envoyé à {$salutation} ({$phone}) - profil #{$profil->id} - Message #{$msgNum}");
                Log::info("Relance profil visa incomplet envoyée : profil#{$profil->id} - {$phone} - msg#{$index}");
                $envoye++;
            } else {
                $this->warn("  ✗ Échec pour {$salutation} ({$phone}) : " . ($result['message'] ?? ''));
                Log::warning("Relance profil visa incomplet échouée : profil#{$profil->id} - {$phone}", $result);
                $echec++;
            }

            usleep(500000); // 0.5 seconde
        }

        return [$envoye, $echec, $ignore];
    }

    /**
     * Extrait le premier numéro de téléphone valide d'un champ contact
     * qui peut contenir plusieurs numéros séparés par " ou ", "/", ",", etc.
     */
    public static function extractFirstPhone(string $contact): string
    {
        $parts = preg_split('/\s+ou\s+|[\/,;\|\s]{1,3}(?=0|\+)/', trim($contact));
        foreach ($parts as $part) {
            $part = trim($part);
            if (!empty($part)) {
                return $part;
            }
        }
        return trim($contact);
    }

    /**
     * Remplace [Prénom] par "M/Mme NOM Prénom" du client CRM dans le message.
     */
    protected function buildMessage(string $template, CRMClient $client): string
    {
        $nom      = trim($client->nom ?? '');
        $prenoms  = trim($client->prenoms ?? '');
        $prenom   = explode(' ', $prenoms)[0]; // premier prénom seulement

        $salutation = trim('M/Mme ' . $nom . ($prenom ? ' ' . $prenom : ''));
        if (empty(trim($nom . $prenom))) {
            $salutation = 'Client';
        }

        return str_replace('[Prénom]', $salutation, $template);
    }
}
