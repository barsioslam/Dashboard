<?php

namespace Models\Log;

use Models\Model;

class ActivityLogModel extends Model {

    protected string $table = 'activity_log';

    const LABELS = [
        'login'                  => ['Connexion',               'ti-login',          'blue'],
        'user_created'           => ['Utilisateur créé',        'ti-user-plus',      'green'],
        'user_updated'           => ['Utilisateur modifié',     'ti-user-edit',      'orange'],
        'user_deleted'           => ['Utilisateur supprimé',    'ti-user-minus',     'red'],
        'user_activated'         => ['Compte activé',           'ti-user-check',     'green'],
        'user_deactivated'       => ['Compte désactivé',        'ti-user-x',         'red'],
        'group_created'          => ['Groupe créé',             'ti-users-plus',     'green'],
        'group_updated'          => ['Groupe modifié',          'ti-users',          'orange'],
        'group_deleted'          => ['Groupe supprimé',         'ti-users-minus',    'red'],
        'group_member_added'     => ['Membre ajouté (groupe)',  'ti-user-plus',      'green'],
        'group_member_removed'   => ['Membre retiré (groupe)',  'ti-user-minus',     'red'],
        'project_created'        => ['Projet créé',             'ti-briefcase',      'green'],
        'project_updated'        => ['Projet modifié',          'ti-briefcase',      'orange'],
        'project_deleted'        => ['Projet supprimé',         'ti-briefcase-off',  'red'],
        'project_archived'       => ['Projet archivé',          'ti-archive',        'muted'],
        'project_unarchived'     => ['Projet désarchivé',       'ti-archive-off',    'blue'],
        'project_member_added'   => ['Membre ajouté (projet)',  'ti-user-plus',      'green'],
        'project_member_removed' => ['Membre retiré (projet)',  'ti-user-minus',     'red'],
        'bug_added'              => ['Bug signalé',             'ti-bug',            'red'],
        'bug_status_updated'     => ['Statut bug mis à jour',   'ti-bug',            'orange'],
        'bug_deleted'            => ['Bug supprimé',            'ti-bug-off',        'muted'],
        'folder_created'         => ['Dossier créé',            'ti-folder-plus',    'green'],
        'folder_renamed'         => ['Dossier renommé',         'ti-folder',         'orange'],
        'folder_deleted'         => ['Dossier supprimé',        'ti-folder-minus',   'red'],
        'file_uploaded'          => ['Fichier uploadé',         'ti-upload',         'green'],
        'file_deleted'           => ['Fichier supprimé',        'ti-trash',          'red'],
    ];

    public static function label(string $action): string {
        return self::LABELS[$action][0] ?? $action;
    }

    public static function icon(string $action): string {
        return self::LABELS[$action][1] ?? 'ti-activity';
    }

    public static function color(string $action): string {
        return self::LABELS[$action][2] ?? 'muted';
    }

    public function getAllFiltered(int $limit, int $offset, string $search = '', string $action = '', int $userId = 0): array {
        [$where, $params] = $this->buildWhere($search, $action, $userId);
        $this->db->query(
            'SELECT al.id, al.action, al.current, al.previous, al.activity_date,
                    al.user_id, u.username
             FROM activity_log al
             LEFT JOIN `user` u ON u.id = al.user_id
             ' . $where . '
             ORDER BY al.activity_date DESC
             LIMIT ' . (int) $limit . ' OFFSET ' . (int) $offset,
            $params
        );
        return $this->db->fetchAll();
    }

    public function countFiltered(string $search = '', string $action = '', int $userId = 0): int {
        [$where, $params] = $this->buildWhere($search, $action, $userId);
        $this->db->query(
            'SELECT COUNT(*) AS total FROM activity_log al LEFT JOIN `user` u ON u.id = al.user_id ' . $where,
            $params
        );
        $result = $this->db->fetchAll();
        return (int) ($result[0]['total'] ?? 0);
    }

    public function getByIdWithUser(int $id): ?array {
        $this->db->query(
            'SELECT al.*, u.username
             FROM activity_log al
             LEFT JOIN `user` u ON u.id = al.user_id
             WHERE al.id = ?',
            [$id]
        );
        return $this->db->fetchAll()[0] ?? null;
    }

    private function buildWhere(string $search, string $action, int $userId): array {
        $clauses = [];
        $params  = [];

        if ($search !== '') {
            $clauses[] = '(al.action LIKE ? OR al.current LIKE ? OR al.previous LIKE ? OR u.username LIKE ?)';
            $like = '%' . $search . '%';
            array_push($params, $like, $like, $like, $like);
        }
        if ($action !== '') {
            $clauses[] = 'al.action = ?';
            $params[]  = $action;
        }
        if ($userId > 0) {
            $clauses[] = 'al.user_id = ?';
            $params[]  = $userId;
        }

        $where = $clauses ? 'WHERE ' . implode(' AND ', $clauses) : '';
        return [$where, $params];
    }

    public function getRecentWithUser(int $limit = 6): array {
        $this->db->query(
            'SELECT al.id, al.action, al.current, al.previous, al.activity_date, al.user_id,
                    u.username
             FROM activity_log al
             LEFT JOIN `user` u ON u.id = al.user_id
             ORDER BY al.activity_date DESC
             LIMIT ' . (int) $limit
        );
        return $this->db->fetchAll();
    }

    public function getLoginsSince(int $since): array {
        $this->db->query(
            "SELECT activity_date
             FROM activity_log
             WHERE action = 'login' AND activity_date >= ?
             ORDER BY activity_date ASC",
            [$since]
        );
        return $this->db->fetchAll();
    }

    public function getForUser(int $userId, int $limit = 8): array {
        $this->db->query(
            'SELECT * FROM `activity_log` WHERE `user_id` = ? ORDER BY `activity_date` DESC LIMIT ' . (int) $limit,
            [$userId]
        );
        return $this->db->fetchAll();
    }

    public function log(string $action, int $actorId, ?string $current = null, ?string $previous = null): void {
        $this->insert([
            'action'        => $action,
            'user_id'       => $actorId,
            'current'       => $current,
            'previous'      => $previous,
            'activity_date' => time(),
        ]);
    }

}
