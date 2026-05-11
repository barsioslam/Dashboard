<?php

namespace Models\Project;

use Models\Model;

class BugStatusModel extends Model {

    protected string $table = 'bug_status';

    // Status constants
    const OPEN        = 0;
    const IN_PROGRESS = 1;
    const WAITING     = 2;
    const CLOSED      = 3;

    const LABELS = [
        self::OPEN        => 'Ouvert',
        self::IN_PROGRESS => 'En cours',
        self::WAITING     => 'En attente',
        self::CLOSED      => 'Fermé',
    ];

    const CSS_CLASS = [
        self::OPEN        => 'red',
        self::IN_PROGRESS => 'orange',
        self::WAITING     => 'yellow',
        self::CLOSED      => 'green',
    ];

    public static function label(int $status): string {
        return self::LABELS[$status] ?? '—';
    }

    public static function cssClass(int $status): string {
        return self::CSS_CLASS[$status] ?? 'muted';
    }

    public function log(int $bugId, int $status, int $updatedBy): void {
        $this->insert([
            'bug_id'      => $bugId,
            'status'      => $status,
            'status_date' => time(),
            'update_by'   => $updatedBy,
        ]);
    }

    public function getForBug(int $bugId): array {
        $this->db->query(
            'SELECT bs.id, bs.status, bs.status_date,
                    u.username AS updated_by_username
             FROM bug_status bs
             LEFT JOIN `user` u ON u.id = bs.update_by
             WHERE bs.bug_id = ?
             ORDER BY bs.status_date DESC',
            [$bugId]
        );
        return $this->db->fetchAll();
    }

}