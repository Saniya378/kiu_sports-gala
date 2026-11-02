<?php

namespace App\Http\Controllers;

use App\Models\Player;
use App\Models\Team;
use App\Services\QrCodeService;
use Illuminate\Contracts\View\View;

class PlayerPassController extends Controller
{
    /**
     * Display the official digital ID card / match pass for an athlete.
     */
    public function show(Player $player, QrCodeService $qrCodeService): View
    {
        $player->load([
            'team.department',
            'team.sport',
            'team.edition',
        ]);

        $verificationUrl = route('players.verify', $player);
        $qrSvg = $qrCodeService->generateSvg($verificationUrl, 180, 1);

        return view('public.players.card', compact('player', 'qrSvg', 'verificationUrl'));
    }

    /**
     * Display printable ID card sheet for an entire team squad.
     */
    public function teamCards(Team $team, QrCodeService $qrCodeService): View
    {
        $team->load([
            'department',
            'sport',
            'edition',
            'players',
        ]);

        $playersWithQr = $team->players->map(function ($player) use ($qrCodeService) {
            $verificationUrl = route('players.verify', $player);
            $player->qr_svg = $qrCodeService->generateSvg($verificationUrl, 130, 1);
            $player->verification_url = $verificationUrl;
            return $player;
        });

        return view('public.teams.id-cards', compact('team', 'playersWithQr'));
    }
}

