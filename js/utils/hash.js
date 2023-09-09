function generateToken(size = 15) {
  var allowedCharacters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
  let token = '';
  
  for (let i = 0; i < size; i++) {
    var randomIndex = Math.floor(Math.random() * allowedCharacters.length);
    token += allowedCharacters.charAt(randomIndex);
  }
  
  return token;
}