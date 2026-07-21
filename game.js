const grid = [
  "########",
  "#......#",
  "#.###..#",
  "#...#.##",
  "#X#....#",
  "########",
];

const rows = grid.length;
const cols = grid[0].length;

// Cari posisi awal (X)
function findStart() {
  for (let r = 0; r < rows; r++) {
    for (let c = 0; c < cols; c++) {
      if (grid[r][c] === "X") {
        return { row: r, col: c };
      }
    }
  }
  return null;
}

// Fungsi bergerak
function move(row, col, dr, dc, steps) {
  for (let i = 0; i < steps; i++) {
    const nr = row + dr;
    const nc = col + dc;

    // Keluar map
    if (nr < 0 || nr >= rows || nc < 0 || nc >= cols) {
      return null;
    }

    // Menabrak tembok
    if (grid[nr][nc] === "#") {
      return null;
    }

    row = nr;
    col = nc;
  }

  return { row, col };
}

// Cetak grid dengan lokasi item ($)
function printGrid(location) {
  const board = grid.map((row) => row.split(""));

  if (location && board[location.row][location.col] === ".") {
    board[location.row][location.col] = "$";
  }

  console.log("\nGrid:");
  board.forEach((row) => console.log(row.join("")));
}

function main(A, B, C) {
  const start = findStart();

  console.log("Start:", start);

  let pos = move(start.row, start.col, -1, 0, A);
  if (!pos) {
    console.log("Invalid move (Up)");
    return;
  }

  pos = move(pos.row, pos.col, 0, 1, B);
  if (!pos) {
    console.log("Invalid path (Right)");
    return;
  }

  pos = move(pos.row, pos.col, 1, 0, C);
  if (!pos) {
    console.log("Invalid path (Down)");
    return;
  }

  console.log("\nPossible Item Location:");
  console.log(`(${pos.row}, ${pos.col})`);

  printGrid(pos);
}

// Contoh
main(3, 4, 2);