# If not running interactively, don't do anything
[[ -z "$PS1" ]] && return

# don't put duplicate lines in the history. See bash(1) for more options
# ... or force ignoredups and ignorespace
HISTCONTROL=ignoredups:ignorespace

# append to the history file, don't overwrite it
shopt -s histappend

# for setting history length see HISTSIZE and HISTFILESIZE in bash(1)
HISTSIZE=1000
HISTFILESIZE=2000

# check the window size after each command and, if necessary,
# update the values of LINES and COLUMNS.
shopt -s checkwinsize

# make less more friendly for non-text input files, see lesspipe(1)
[[ -x /usr/bin/lesspipe ]] && eval "$(SHELL=/bin/sh lesspipe)"

################################################################################

# Reset
Color_Off="\[\033[0m\]";       # Text Reset

# Regular Colors
Black="\[\033[0;30m\]";        # Black
Red="\[\033[0;31m\]";          # Red
Green="\[\033[0;32m\]";        # Green
Yellow="\[\033[0;33m\]";       # Yellow
Blue="\[\033[0;34m\]";         # Blue
Purple="\[\033[0;35m\]";       # Purple
Cyan="\[\033[0;36m\]";         # Cyan
White="\[\033[0;37m\]";        # White

# Bold
BBlack="\[\033[1;30m\]";       # Black
BRed="\[\033[1;31m\]";         # Red
BGreen="\[\033[1;32m\]";       # Green
BYellow="\[\033[1;33m\]";      # Yellow
BBlue="\[\033[1;34m\]";        # Blue
BPurple="\[\033[1;35m\]";      # Purple
BCyan="\[\033[1;36m\]";        # Cyan
BWhite="\[\033[1;37m\]";       # White

# Underline
UBlack="\[\033[4;30m\]";       # Black
URed="\[\033[4;31m\]";         # Red
UGreen="\[\033[4;32m\]";       # Green
UYellow="\[\033[4;33m\]";      # Yellow
UBlue="\[\033[4;34m\]";        # Blue
UPurple="\[\033[4;35m\]";      # Purple
UCyan="\[\033[4;36m\]";        # Cyan
UWhite="\[\033[4;37m\]";       # White

# Background
On_Black="\[\033[40m\]";       # Black
On_Red="\[\033[41m\]";         # Red
On_Green="\[\033[42m\]";       # Green
On_Yellow="\[\033[43m\]";      # Yellow
On_Blue="\[\033[44m\]";        # Blue
On_Purple="\[\033[45m\]";      # Purple
On_Cyan="\[\033[46m\]";        # Cyan
On_White="\[\033[47m\]";       # White

# High Intensty
IBlack="\[\033[0;90m\]";       # Black
IRed="\[\033[0;91m\]";         # Red
IGreen="\[\033[0;92m\]";       # Green
IYellow="\[\033[0;93m\]";      # Yellow
IBlue="\[\033[0;94m\]";        # Blue
IPurple="\[\033[0;95m\]";      # Purple
ICyan="\[\033[0;96m\]";        # Cyan
IWhite="\[\033[0;97m\]";       # White

# Bold High Intensty
BIBlack="\[\033[1;90m\]";      # Black
BIRed="\[\033[1;91m\]";        # Red
BIGreen="\[\033[1;92m\]";      # Green
BIYellow="\[\033[1;93m\]";     # Yellow
BIBlue="\[\033[1;94m\]";       # Blue
BIPurple="\[\033[1;95m\]";     # Purple
BICyan="\[\033[1;96m\]";       # Cyan
BIWhite="\[\033[1;97m\]";      # White

# High Intensty backgrounds
On_IBlack="\[\033[0;100m\]";   # Black
On_IRed="\[\033[0;101m\]";     # Red
On_IGreen="\[\033[0;102m\]";   # Green
On_IYellow="\[\033[0;103m\]";  # Yellow
On_IBlue="\[\033[0;104m\]";    # Blue
On_IPurple="\[\033[10;95m\]";  # Purple
On_ICyan="\[\033[0;106m\]";    # Cyan
On_IWhite="\[\033[0;107m\]";   # White

# Various variables you might want for your PS1 prompt instead
Time12h="\T-\h";
Time12a="\@";
PathShort="\w";
PathFull="\W";
NewLine="\n";
Jobs="\j";

function parse_git_branch () {
    git branch 2> /dev/null | sed -e '/^[^*]/d' -e 's/* \(.*\)/ (\1)/';
}

# PS1="$Cyan$PathShort$Green\$(parse_git_branch)${NewLine}➜ $Color_Off ";
PS1="${BGreen}\u@${HOSTNAME}: ${Cyan}\`pwd\`${Blue}$(parse_git_branch)${NewLine}${Green} -> ${Color_Off} ";

# enable color support of ls and also add handy aliases
if [[ -x /usr/bin/dircolors ]]; then
    test -r ~/.dircolors && eval "$(dircolors -b ~/.dircolors)" || eval "$(dircolors -b)"
    alias ls='ls --color=auto'
    #alias dir='dir --color=auto'
    #alias vdir='vdir --color=auto'

    alias grep='grep --color=auto'
    alias fgrep='fgrep --color=auto'
    alias egrep='egrep --color=auto'
fi

# some more ls aliases
alias la='ls -A'
alias l='ls -CF'
alias ls="ls -CFah"
alias ll="ls -lah"
