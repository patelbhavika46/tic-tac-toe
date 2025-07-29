<?php
session_start(); // Start the session to store game state

// --- 1. Initialize or Reset Game State ---
if (!isset($_SESSION['board']) || isset($_POST['reset_game'])) {
    $_SESSION['board'] = array_fill(0, 9, ' '); // 0-8 for cells, initially empty
    $_SESSION['current_player'] = 'X'; // Human player starts
    $_SESSION['game_over'] = false;
    $_SESSION['winner'] = null;
    $_SESSION['message'] = "Welcome to Tic-Tac-Toe! Player X's turn.";
}

// --- 2. Game Logic Functions ---

/**
 * Displays the Tic-Tac-Toe board as an HTML table.
 */
function display_board($board) {
    echo '<form method="POST" action="">'; // Form to submit moves
    echo '<table border="1" style="border-collapse: collapse; margin: 20px auto;">';
    for ($i = 0; $i < 3; $i++) {
        echo '<tr>';
        for ($j = 0; $j < 3; $j++) {
            $pos = $i * 3 + $j;
            $cell_content = $board[$pos] == ' ' ? '&nbsp;' : $board[$pos];
            echo '<td style="width: 60px; height: 60px; text-align: center; vertical-align: middle; font-size: 2em;">';
            if ($board[$pos] == ' ' && !$_SESSION['game_over']) {
                // If cell is empty and game not over, make it clickable
                echo '<button type="submit" name="move" value="' . $pos . '" style="width: 100%; height: 100%; font-size: 1.5em; cursor: pointer;">' . $cell_content . '</button>';
            } else {
                echo $cell_content;
            }
            echo '</td>';
        }
        echo '</tr>';
    }
    echo '</table>';
    echo '</form>';
}

/**
 * Checks if a player has won.
 * @param array $board The current game board.
 * @param string $player The player symbol ('X' or 'O').
 * @return bool True if the player has won, false otherwise.
 */
function check_win($board, $player) {
    $win_conditions = [
        [0, 1, 2], [3, 4, 5], [6, 7, 8], // Rows
        [0, 3, 6], [1, 4, 7], [2, 5, 8], // Columns
        [0, 4, 8], [2, 4, 6]            // Diagonals
    ];
    foreach ($win_conditions as $condition) {
        if ($board[$condition[0]] == $player &&
            $board[$condition[1]] == $player &&
            $board[$condition[2]] == $player) {
            return true;
        }
    }
    return false;
}

/**
 * Checks if the game is a draw.
 * @param array $board The current game board.
 * @return bool True if it's a draw, false otherwise.
 */
function check_draw($board) {
    return !in_array(' ', $board); // No empty spaces left
}

/**
 * Handles a player's move.
 * @param int $position The chosen board position (0-8).
 * @param string $player The player making the move.
 */
function make_move($position, $player) {
    if ($_SESSION['board'][$position] == ' ' && !$_SESSION['game_over']) {
        $_SESSION['board'][$position] = $player;
        if (check_win($_SESSION['board'], $player)) {
            $_SESSION['game_over'] = true;
            $_SESSION['winner'] = $player;
            $_SESSION['message'] = "Player " . $player . " wins!";
        } elseif (check_draw($_SESSION['board'])) {
            $_SESSION['game_over'] = true;
            $_SESSION['winner'] = 'Draw';
            $_SESSION['message'] = "It's a draw!";
        } else {
            $_SESSION['current_player'] = ($player == 'X') ? 'O' : 'X'; // Switch player
            $_SESSION['message'] = "Player " . $_SESSION['current_player'] . "'s turn.";
        }
    } else {
        $_SESSION['message'] = "Invalid move. Choose an empty cell.";
    }
}

// --- 3. AI Logic Functions (Starting with Random AI) ---

/**
 * AI makes a random valid move.
 * @param array $board The current game board.
 * @param string $ai_player The AI's symbol.
 * @return int The chosen position.
 */
function get_random_ai_move($board, $ai_player) {
    $available_moves = [];
    foreach ($board as $index => $cell) {
        if ($cell == ' ') {
            $available_moves[] = $index;
        }
    }
    if (!empty($available_moves)) {
        return $available_moves[array_rand($available_moves)];
    }
    return -1; // No moves available (should be caught by check_draw)
}

// --- 4. Handle Incoming Moves and AI Turn ---
if (isset($_POST['move']) && !$S_SESSION['game_over']) {
    $player_move_pos = (int)$_POST['move'];
    make_move($player_move_pos, $_SESSION['current_player']);

    // If game is not over and it's AI's turn (assuming AI is 'O')
    if (!$_SESSION['game_over'] && $_SESSION['current_player'] == 'O') {
        // A brief delay to make it feel like AI is "thinking"
        // sleep(1); // Not good for web servers, but illustrates timing
        $ai_move_pos = get_random_ai_move($_SESSION['board'], 'O');
        if ($ai_move_pos != -1) {
            make_move($ai_move_pos, 'O');
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tic-Tac-Toe PHP AI</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; margin-top: 50px; }
        h1 { color: #333; }
        .message { margin-bottom: 20px; font-size: 1.2em; color: #007bff; }
        button { padding: 10px 20px; font-size: 1em; cursor: pointer; }
        .reset-button { margin-top: 20px; }
    </style>
</head>
<body>
    <h1>Tic-Tac-Toe (PHP + AI)</h1>

    <div class="message"><?php echo $_SESSION['message']; ?></div>

    <?php display_board($_SESSION['board']); ?>

    <?php if ($_SESSION['game_over']): ?>
        <form method="POST" action="" class="reset-button">
            <button type="submit" name="reset_game">Play Again</button>
        </form>
    <?php endif; ?>
</body>
</html>