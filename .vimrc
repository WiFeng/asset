
syn on
filetype on

" Color scheme
"colorscheme evening
colorscheme molokai

"set number
"set nowrap

set shiftwidth=4
set softtabstop=4 
set tabstop=4
set laststatus=2

"set expandtab
set autoindent

set list
set listchars=tab:>-,trail:-

set encoding=utf-8
"set noendofline binary
set fileformats=unix,dos

" Terminal Color
set t_Co=256
" Highlight current line
set cursorline cursorcolumn


nnoremap <silent> <F5> :NERDTree<CR>
nnoremap <silent> <F8> :TagbarToggle<CR>

noremap <silent><tab>m :tabnew<cr>
noremap <silent><tab>e :tabclose<cr>
noremap <silent><tab>n :tabn<cr>
noremap <silent><tab>p :tabp<cr>
noremap <silent><leader>t :tabnew<cr>
noremap <silent><leader>g :tabclose<cr>
noremap <silent><leader>1 :tabn 1<cr>
noremap <silent><leader>2 :tabn 2<cr>
noremap <silent><leader>3 :tabn 3<cr>
noremap <silent><leader>4 :tabn 4<cr>
noremap <silent><leader>5 :tabn 5<cr>
noremap <silent><leader>6 :tabn 6<cr>
noremap <silent><leader>7 :tabn 7<cr>
noremap <silent><leader>8 :tabn 8<cr>
noremap <silent><leader>9 :tabn 9<cr>
noremap <silent><leader>0 :tabn 10<cr>
noremap <silent><s-tab> :tabnext<CR>
inoremap <silent><s-tab> <ESC>:tabnext<CR>

autocmd FileType c,cpp,python,ruby,java,sh,html,javascript autocmd BufWritePre <buffer> :%s/\s\+$//e
autocmd VimEnter * :NERDTree

