#!/bin/bash
# Drives the Bitrix install wizard's AJAX sub-step mechanism (CAjaxForm) for the
# "create_modules" step, and falls back to full-page stepping once the step changes.
set -u
BASE="http://127.0.0.1:8081/"
COOKIES=/tmp/wiz_cookies.txt
FIELDS_FILE=/tmp/wiz_fields.txt   # name=value per line, current form state
LOG=/tmp/wiz_drive.log

> "$LOG"

load_fields_from_html() {
  local infile=$1
  > "$FIELDS_FILE"
  grep -o '<input type="hidden" name="[^"]*" value="[^"]*"' "$infile" | \
    sed -n 's/.*name="\([^"]*\)" value="\([^"]*\)".*/\1\t\2/p' > "$FIELDS_FILE"
}

set_field() {
  local name=$1 value=$2
  if grep -q "^${name}	" "$FIELDS_FILE"; then
    sed -i "s|^${name}\t.*|${name}\t${value}|" "$FIELDS_FILE"
  else
    echo -e "${name}\t${value}" >> "$FIELDS_FILE"
  fi
}

get_field() {
  local name=$1
  grep "^${name}	" "$FIELDS_FILE" | head -1 | cut -f2
}

post_fields() {
  local outfile=$1
  local args=()
  while IFS=$'\t' read -r name value; do
    [ -n "$name" ] && args+=("-F" "${name}=${value}")
  done < "$FIELDS_FILE"
  args+=("-F" "StepNext=Далее")
  curl -s -c "$COOKIES" -b "$COOKIES" -X POST "$BASE" "${args[@]}" -o "$outfile"
}

load_fields_from_html "${1:-/tmp/wiz_step5.html}"
TARGET_STEP="${2:-create_modules}"
echo "Initial CurrentStepID=$(get_field CurrentStepID)" >> "$LOG"

max_iter=300
for i in $(seq 1 $max_iter); do
  current_step=$(get_field CurrentStepID)
  post_fields /tmp/wiz_ajax_out.html

  body=$(cat /tmp/wiz_ajax_out.html)

  if echo "$body" | grep -q '\[response\]'; then
    inner=$(echo "$body" | sed -n 's/.*\[response\](.*)\[\/response\].*/\1/p')
    inner=$(echo "$body" | sed -e 's/.*\[response\]//' -e 's/\[\/response\].*//')
    nextStep=$(echo "$inner" | grep -o "'nextStep'\s*:\s*'[^']*'" | sed "s/.*: *'//;s/'$//")
    nextStepStage=$(echo "$inner" | grep -o "'nextStepStage'\s*:\s*'[^']*'" | sed "s/.*: *'//;s/'$//")
    statusMsg=$(echo "$inner" | grep -o "Post({[^}]*}, *'[^']*'" | sed "s/.*, *'//;s/'$//")
    pct=$(echo "$inner" | grep -o "SetStatus('[0-9]*'" | grep -o "[0-9]*")
    echo "iter $i: step=$current_step pct=$pct status=$statusMsg next=$nextStep/$nextStepStage" >> "$LOG"

    if [ -z "$nextStep" ]; then
      echo "iter $i: no nextStep found in ajax response, dumping inner:" >> "$LOG"
      echo "$inner" >> "$LOG"
      break
    fi

    set_field "__wiz_nextStep" "$nextStep"
    set_field "__wiz_nextStepStage" "$nextStepStage"
  else
    # Full page response -> step likely changed; reload fields from this page
    load_fields_from_html /tmp/wiz_ajax_out.html
    new_step=$(get_field CurrentStepID)
    echo "iter $i: FULL PAGE RESPONSE, new CurrentStepID=$new_step" >> "$LOG"
    cp /tmp/wiz_ajax_out.html "/tmp/wiz_page_${new_step}.html"
    if [ "$new_step" != "$current_step" ]; then
      echo "iter $i: step advanced from $current_step to $new_step" >> "$LOG"
    fi
    if [ "$new_step" == "$TARGET_STEP" ] && [ "$current_step" == "$TARGET_STEP" ]; then
      echo "iter $i: stuck on $TARGET_STEP with full page, stopping" >> "$LOG"
      break
    fi
    if [ "$new_step" != "$TARGET_STEP" ]; then
      echo "iter $i: left $TARGET_STEP stage, stopping driver" >> "$LOG"
      break
    fi
  fi
done

echo "DRIVE LOOP DONE" >> "$LOG"
