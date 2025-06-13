<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Game;
use App\Models\Grid;

class GameController extends Controller
{
    /**
     * Reset a minesweeper game by clearing the grid and placing new mines.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $gameId
     * @return \Illuminate\Http\JsonResponse
     */
    public function reset(Request $request, $gameId)
    {
        $game = Game::find($gameId);

        if (!$game) {
            return response()->json(['message' => 'Game not found'], 404);
        }

        // Remove previous grid
        Grid::where('game_id', $game->id)->delete();

        // Reset game fields
        $game->status = 'OPEN';
        $game->start_at = now();
        $game->end_at = null;
        $game->save();

        // Recreate grid with new random mines
        generateGrid($game->rows, $game->columns, $game->mines, $game->id);

        return response()->json(['message' => 'Game reset successfully'], 200);
    }
}
